<?php

namespace SE\TransferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Form\UserInputType;
use Symfony\Component\HttpFoundation\Request;
use SE\InputBundle\Entity\Transfer;
use SE\InputBundle\Form\TransferType;
use SE\InputBundle\Entity\Notification;

class TransferController extends Controller
{
	private function getUsrDepId(){
		$usr= $this->get('security.context')->getToken()->getUser();
		$usrTeam = $usr->getTeam();
		$usrDepId = -1;
		if ($usrTeam != null)
			$usrDepId = $usrTeam->getDepartement()->getId();
		return $usrDepId;
	}
	
    public function indexAction(Request $request)
    {
    	$listTransfers = $this->getDoctrine()
      		->getManager()
      		->getRepository('SEInputBundle:Transfer')
      		->getAll()
    	;
    	$confirmed = 0;
    	$refused = 0;
    	$pending = 0;
    	$now = (new \DateTime("now"))->format("m-Y");
    	foreach ($listTransfers as $tr){
    		if ($tr->getDateStart()->format("m-Y") == $now){
    			if ($tr->getValidated() == 0)
    				$pending += 1;
    			else if ($tr->getValidated() == 1)
    				$confirmed += 1;
    			else
    				$refused += 1;
    		}
    	}

    	$listEmployees = $this->getDoctrine()
      		->getManager()
      		->getRepository('SEInputBundle:Employee')
      		->getCurrentEmployees()
    	;

    	$listTeams = $this->getDoctrine()
      		->getManager()
      		->getRepository('SEInputBundle:Team')
      		->getCurrentTeams()
    	;
    	
      	$usrDepId = $this->getUsrDepId();
    	
      	$userInput = new UserInput();
    	$form = $this->createForm(new UserInputType(), $userInput);
    	
        return $this->render('SETransferBundle:Transfer:index.html.twig',
        		array('listEmployees' => $listEmployees,
        				'listTeams' => $listTeams,
        				'listTransfers' => $listTransfers,
        				'usrDepId' => $usrDepId,
        				'form' => $form->createView(),
        				'refused' => $refused,
        				'confirmed' => $confirmed,
        				'pending' => $pending,
        				'monthlytotal' => $refused + $confirmed + $pending
        		));
    }
    
    public function addTransferAction(Request $request)
	{
      	$usrDepId = $this->getUsrDepId();
		// Build the form
		$transfer = new Transfer();
		$transfer->setValidated(false);
		$form = $this->createForm($this->get('inputBundle_addtransfer'), $transfer, array('max_length' => $usrDepId));
		//$form = new TransferType();
		//$form->buildForm($this->createFormBuilder(), array('data' => $transfer, 'option1' => $usrDepId));
		
		// Handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
		
			// Save the User
			$em = $this->getDoctrine()->getManager();
			$em->persist($transfer);
		
			$notif = new Notification();
			$usr = $this->get('security.context')->getToken()->getUser();
			$notif->setSender($usr);
			$notif->setReceiver($usr);//todo
			$notif->setTitle('Transfer demand');
			$notif->setText($usr->getName().' demands the transfer of '
					.$transfer->getEmployee()->getNameDepartement().' to '
					.$transfer->getDepartement()->getName()." the "
					.$transfer->getDateStartString()
					.". You have to confirm/invalidate this transfer.");//todo add link

			$em->persist($notif);
			$em->flush();
		
			return $this->redirectToRoute('se_transfer_homepage');
		}
		
		return $this->render(
				'SETransferBundle:Transfer:addTransfer.html.twig',
				array('form' => $form->createView())
				);
	}

    public function menuAction()
	{
		return $this->render('SETransferBundle:Transfer:menu.html.twig');
	}
	
	public function acceptAction($transfer_id){

		$em = $this->getDoctrine()->getManager();
		$transfer = $em->find(Transfer::class, $transfer_id);

		$usrDepId = $this->getUsrDepId();
		
      	if ($usrDepId == $transfer->getDepartement()->getId()){
			$transfer->setValidated(1);
			$em->persist($transfer);
		
			$notif = new Notification();
			$usr = $this->get('security.context')->getToken()->getUser();
			$notif->setSender($usr);
			$notif->setReceiver($usr);//todo
			$notif->setTitle('Transfer confirmed');
			$notif->setText($usr->getName().' confirmed the transfer of '
					.$transfer->getEmployee()->getNameDepartement().' to '
					.$transfer->getDepartement()->getName()." the "
					.$transfer->getDateStartString().".");

			$em->persist($notif);
			$em->flush();
      	}
		
		return $this->redirectToRoute('se_transfer_homepage');
	}
	
	public function refuseAction($transfer_id){

		$em = $this->getDoctrine()->getManager();
		$transfer = $em->find(Transfer::class, $transfer_id);

		$usrDepId = $this->getUsrDepId();
		
      	if ($usrDepId == $transfer->getDepartement()->getId()){
			$transfer->setValidated(2);
			$em->persist($transfer);
		
			$notif = new Notification();
			$usr = $this->get('security.context')->getToken()->getUser();
			$notif->setSender($usr);
			$notif->setReceiver($usr);//todo
			$notif->setTitle('Transfer refused');
			$notif->setText($usr->getName().' refused the transfer of '
					.$transfer->getEmployee()->getNameDepartement().' to '
					.$transfer->getDepartement()->getName()." the "
					.$transfer->getDateStartString().".");

			$em->persist($notif);
			$em->flush();
      	}

		return $this->redirectToRoute('se_transfer_homepage');
	}
}
