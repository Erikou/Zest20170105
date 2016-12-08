<?php

namespace SE\InputBundle\Controller;

use SE\InputBundle\Entity\Employee;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Form\UserInputType;
use SE\InputBundle\Entity\InputReview;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SE\InputBundle\SAP;
use SE\InputBundle\Entity\Permission;
use SE\InputBundle\Entity\Abilitation;
use Symfony\Component\Validator\Constraints\Time;
use SE\InputBundle\Entity\Transfer;
use SE\InputBundle\Entity\Notification;

class EntryController extends Controller
{
	public function inputAction(Request $request)
	{
    	$listEmployees = $this->getDoctrine()
      		->getManager()
      		->getRepository('SEInputBundle:Employee')
      		->getAlphaCurrentEmployees()
    	;

    	$userInput = new UserInput();
    	$form = $this->createForm(new UserInputType(), $userInput);

    	if ($form->handleRequest($request)->isValid()) {
      		$em = $this->getDoctrine()->getManager();
      		
      		foreach ($userInput->getInputEntries() as $inputentry){
      			foreach ($inputentry->getActivityHours() as $activityhour){
      				if ($activityhour->getActivity()->getId() == 13){// if is a transfer
      					$transfer = new Transfer();
      					$transfer->setDemand($this->get('security.context')->getToken()->getUser());
      					$transfer->setEmployee($inputentry->getEmployee());
      					$transfer->setTeam($activityhour->getTransferTeam());
      					$transfer->setShift($userInput->getShift());
      					$transfer->setDateStart($userInput->getDateInput());
      					$transfer->setValidated(false);
      					$transfer->setInputEntry($inputentry);
      					$transfer->setTotalHours($activityhour->getRegularHours()+$activityhour->getOtHours());
      					$em->persist($transfer);
      					$this->transferNotice($transfer, $em);
      				}
      			}
      		}      		
      		$em->persist($userInput);
      		
      		$now = new \DateTime();
      		$ad = $em->getRepository('SEInputBundle:Attendancedata')->getDate($now->format("Y"), $now->format("m"))[0];
      		$ad->setRefresher(0);
      		$em->remove($ad);
      		$em->flush();

    		$request->getSession()->getFlashBag()->add('notice', 'new working hours entry saved');

    		return $this->redirectToRoute('se_input_home');
   		}

    	return $this->render('SEInputBundle:Entry:input_form.html.twig', array(
    	'form' => $form->createView(),
    	'listEmployees' => $listEmployees
    	));
	}


	public function transferNotice(Transfer $transfer, $em){
		$user = $this->get('security.context')->getToken()->getUser();
			
		$receivers = [];
		$listUsers = $em->getRepository('SEInputBundle:User')->getAll();
			
		// Find all users who can accept the transfer and create notifications
		foreach ($listUsers as $usr){
			$t = $usr->getTeam();
			if ($t != null && $t->getDepartement() == $transfer->getDepartement()){
				if (in_array('ROLE_TRANSFER_EDIT', $usr->getRoles(), true)){
					$notif = new Notification();
					$notif->setDateCreation(new \DateTime("now"));
					$notif->setSender($user);
					$notif->setReceiver($usr);
					$receivers[] = $usr->getName();
					$notif->setTitle('Transfer demand');
					$notif->setText($user->getName().' demands the transfer of '
							.$transfer->getEmployee()->getNameDepartement().' to '
							.$transfer->getDepartement()->getName()." the "
							.$transfer->getDateStartString()
							.". You have to confirm/invalidate this transfer.");//todo add link
	
							$em->persist($notif);
				}
			}
		}
			
		// If no manager is registered, the administrators are warned via notifications
		if (empty($receivers)){
			$receivers[] = "no one (no manager has been set for this department. The administrator has been warned.)";
			foreach ($listUsers as $usr){
				if (in_array('ROLE_ADMIN', $usr->getRoles(), true)){
					$notif = new Notification();
					$notif->setDateCreation(new \DateTime("now"));
					$notif->setSender($user);
					$notif->setReceiver($usr);
					$notif->setTitle('Manager missing');
					$notif->setText('No user in the '
							.$transfer->getDepartement()->getName()
							." department has the right to confirm transfers.");
	
					$em->persist($notif);
				}
			}
		}
			
		// A feedback notification is created for the demander
		$notif = new Notification();
		$notif->setDateCreation(new \DateTime("now"));
		$notif->setSender($user);
		$notif->setReceiver($user);
		$notif->setTitle('Transfer demand sent');
		$notif->setText('You have sent a demand of transfer of '
				.$transfer->getEmployee()->getNameDepartement().' to '
				.$transfer->getDepartement()->getName()." the "
				.$transfer->getDateStartString()
				." to ".implode(", ", $receivers)
				.". Wait until they confirm/invalidate this transfer.");
	
		$em->persist($notif);
	}
	
	public function menuAction()
  	{
    	return $this->render('SEInputBundle:Entry:menu.html.twig');
  	}

  	public function welcomeAction()
    {
    	/*$em = $this->getDoctrine()->getManager();
    	$perm1 = new Permission();
    	$perm1->setName("ROLE_INPUT_READ");
    	$perm1->setDescription("input read");
    	$perm2 = new Permission();
    	$perm2->setName("ROLE_INPUT_COMMENT");
    	$perm2->setDescription("input comment");
    	$abi = new Abilitation();
    	$abi->setName("input commenter");
    	$abi->setDescription("can read and comment inputs");
    	$abi->addPermission($perm1);
    	$abi->addPermission($perm2);
    	$usr = $this->get('security.context')->getToken()->getUser();
    	$usr->addAbilitation($abi);

    	$em->persist($perm1);
    	$em->persist($perm2);
    	$em->persist($abi);
    	$em->persist($usr);
    	$em->flush();*/
    	
      	return $this->render('SEInputBundle:Entry:welcome.html.twig');
    }

  	public function populateAction()
  	{ 
    	$em = $this->getDoctrine()->getManager();
    	$request = $this->get('request');        
    	$idEmployee = $request->get('idEmployee');
    
    	$addEmployee = $em->getRepository('SEInputBundle:Employee')->findOneBy(array('id' => $idEmployee, 'statusControl' => 1));
    	if($addEmployee){
      		$sesa = $addEmployee->getSesa();
      		$activity = $addEmployee->getDefaultActivity()->getId();
      		$response = array("code" => 100, "success" => true, "sesa" => $sesa, "activity" => $activity);
	
    	}else{
      		$response = array("code" => 400, "success" => false);
    	}

    	return new Response(json_encode($response)); 
  	}
}
