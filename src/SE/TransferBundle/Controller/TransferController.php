<?php

namespace SE\TransferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Form\UserInputType;
use Symfony\Component\HttpFoundation\Request;
use SE\InputBundle\Entity\Transfer;

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
        				'form' => $form->createView()
        		));
    }
    
    public function addTransferAction(Request $request)
	{
      	$usrDepId = $this->getUsrDepId();
		// Build the form
		$transfer = new Transfer();
		$transfer->setValidated(false);
		$form = $this->createForm($this->get('inputBundle_addtransfer'), $transfer, array());
		
		// Handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
		
			// Save the User
			$em = $this->getDoctrine()->getManager();
			$em->persist($transfer);
			$em->flush();
		
			// ... do any other work - like sending them an email, etc
			// maybe set a "flash" success message for the user
		
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
			$transfer->setValidated(true);
			$em->persist($transfer);
			$em->flush();
      	}
		
		return $this->redirectToRoute('se_transfer_homepage');
	}
	
	public function refuseAction($transfer_id){

		$em = $this->getDoctrine()->getManager();
		$transfer = $em->find(Transfer::class, $transfer_id);

		$usrDepId = $this->getUsrDepId();
		
      	if ($usrDepId == $transfer->getDepartement()->getId()){
			$em->remove($transfer);
			$em->flush();
      	}

		return $this->redirectToRoute('se_transfer_homepage');
	}
}
