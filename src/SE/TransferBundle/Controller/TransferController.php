<?php

namespace SE\TransferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Form\UserInputType;
use Symfony\Component\HttpFoundation\Request;
use SE\InputBundle\Entity\Transfer;
use SE\InputBundle\Form\TransferType;
use SE\InputBundle\Entity\Notification;
use SE\InputBundle\Form\EmployeeType;
use SE\InputBundle\Entity\Employee;
use SE\InputBundle\Entity\EmployeeRepository;
use SE\InputBundle\Entity\DepartementRepository;

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
	private function getUsrDep(){
		$usr= $this->get('security.context')->getToken()->getUser();
		$usrTeam = $usr->getTeam();
		$usrDep = null;
		if ($usrTeam != null)
			$usrDep = $usrTeam->getDepartement();
		return $usrDep;
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
    	
      	$usrDepId = $this->getUsrDepId();
    	
    	$form = $this->createFormBuilder()
        		->add('tag', 'entity', array(
            		'class' => 'SEInputBundle:Employee',
        			'property' => 'name',
            		'multiple' => true,
            		'expanded' => false,
            		'query_builder' => function(EmployeeRepository $er){
                		return $er->createQueryBuilder('u')->orderBy('u.name', 'DESC');},
        		))
        		->add('Filter', 'submit')
        		->getForm();
    	
        return $this->render('SETransferBundle:Transfer:index.html.twig',
        		array('listTransfers' => $listTransfers,
        				'usrDepId' => $usrDepId,
        				'form' => $form->createView(),
        				'refused' => $refused,
        				'confirmed' => $confirmed,
        				'pending' => $pending,
        				'monthlytotal' => $refused + $confirmed + $pending
        		));
    }

    public function historyAction(Request $request)
    {
    	$listTransfers = $this->getDoctrine()
    	->getManager()
    	->getRepository('SEInputBundle:Transfer')
    	->getAll()
    	;
    	 
    	$form = $this->createFormBuilder()
    	->add('from', 'entity', array(
    			'class' => 'SEInputBundle:Departement',
    			'property' => 'name',
    			'multiple' => true, 'expanded' => true,
    			'required' => false,
    			'query_builder' => function(DepartementRepository $er){
    			return $er->createQueryBuilder('u')->orderBy('u.name', 'ASC');},
    			))
    	->add('to', 'entity', array(
    			'class' => 'SEInputBundle:Departement',
    			'property' => 'name',
    			'multiple' => true, 'expanded' => true,
    			'required' => false,
    			'query_builder' => function(DepartementRepository $er){
    			return $er->createQueryBuilder('u')->orderBy('u.name', 'ASC');},
    			))
    	->add('employee', 'entity', array(
    			'class' => 'SEInputBundle:Employee',
    			'property' => 'nameDepartement',
    			'multiple' => true, 'expanded' => false,
    			'required' => false,
    			'query_builder' => function(EmployeeRepository $er){
    			return $er->createQueryBuilder('u')->orderBy('u.name', 'ASC');},
    			))
    			->add('Filter', 'submit')
    			->getForm();
    	
    	$form->handleRequest($request);
    	if ($form->isValid()) {    			
    		$listA = [];
    		if (sizeof($form->get('employee')->getData()) == 0){ // No employee specified
    			$listA = $listTransfers;
    		} else {
	    		foreach ($listTransfers as $tr){
	    			foreach ($form->get('employee')->getData() as $emp){
	    				if ($emp->getId() == $tr->getEmployee()->getId()){
	    					$listA[] = $tr;
	    					break;
	    				}
	    			}
	    		}
    		}    			
    		$listB = [];
    		if (sizeof($form->get('from')->getData()) == 0){ // No department specified
    			$listB = $listA;
    		} else {
	    		foreach ($listA as $tr){
	    			foreach ($form->get('from')->getData() as $dep){
	    				if ($dep->getId() == $tr->getEmployee()->getDefaultTeam()->getDepartement()->getId()){
	    					$listB[] = $tr;
	    					break;
	    				}
	    			}
	    		}
    		}     			
    		$listC = [];
    		if (sizeof($form->get('to')->getData()) == 0){ // No department specified
    			$listC = $listB;
    		} else {
	    		foreach ($listB as $tr){
	    			foreach ($form->get('to')->getData() as $dep){
	    				if ($dep->getId() == $tr->getDepartement()->getId()){
	    					$listC[] = $tr;
	    					break;
	    				}
	    			}
	    		}
    		}   
    		
    		$listTransfers = $listC;
    	}
    			 
    	return $this->render('SETransferBundle:Transfer:history.html.twig',
    					array(
    							'listTransfers' => $listTransfers,
    							'form' => $form->createView(),
    					));
    }
    
    public function employeesAction()
    {
    	$usrDep = $this->getUsrDep();
    	
    	$listEmployees = $this->getDoctrine()
    	->getManager()
    	->getRepository('SEInputBundle:Employee')
    	->getCurrentEmployees()
    	;
    	
    	$listTransfers = $this->getDoctrine()
    	->getManager()
    	->getRepository('SEInputBundle:Transfer')
    	->getValidToday()
    	;

    	foreach ($listEmployees as $emp){
    		$emp->setTmpDep($emp->getDefaultTeam()->getDepartement());
    		foreach ($listTransfers as $tr){
    			if ($tr->getEmployee() == $emp){
    				$emp->setTmpDep($tr->getDepartement());
    			}
    		}
    	}
    			 
    	return $this->render('SETransferBundle:Transfer:employees.html.twig',
    					array('listEmployees' => $listEmployees,
    							'usrDepId' => ($usrDep ? $usrDep->getId() : -1)
    					));
    }
    
    public function addTransferAction(Request $request)
	{
      	$usrDepId = $this->getUsrDepId();
		// Build the form
		$transfer = new Transfer();
		$transfer->setValidated(false);
		$form = $this->createFormBuilder($transfer)
		->add('date_start', 'date', array('error_bubbling' => true))
		->add('team', 'entity', array(
				'class' => 'SEInputBundle:Team',
				'property' => 'name',
				'error_bubbling' => true
		))
		->add('shift', 'entity', array(
				'class' => 'SEInputBundle:Shift',
				'property' => 'identifier',
				'error_bubbling' => true
		))
		->getForm();
		
		// Handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$transfer = new Transfer();
			$transfer->setValidated(false);
			$transfer->setShift($form->get("shift")->getData());
			$transfer->setTeam($form->get("team")->getData());
			$transfer->setDepartement($transfer->getTeam()->getDepartement());
			$transfer->setDateStart($form->get("date_start")->getData());
			
			return $this->redirectToRoute('se_transfer_add2', array('transfer' => $transfer, 'request' => null));
		}
		
		return $this->render(
				'SETransferBundle:Transfer:addTransfer.html.twig',
				array('form' => $form->createView())
				);
	}
    
    
    public function addTransfer2Action(Request $request, Transfer $transfer)
	{
      	$usrDepId = $this->getUsrDepId();
		// Build the form
		$form = $this->createFormBuilder($transfer)
		->add('employee', 'entity', array(
				'class' => 'SEInputBundle:Employee',
    			'query_builder' => function(EmployeeRepository $er) use ($usrDepId) {
        			return $er->getDepartementEmployeesDate($usrDepId, $transfer->getDateStart());
    			},
				'property' => 'nameDepartement',
				'error_bubbling' => true
		))
		->getForm();
		
		// Handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			
			$user = $this->get('security.context')->getToken()->getUser();
		
			// Save the new Transfer Demand
			$transfer->setDemand($user);
			$transfer->setDepartement($transfer->getTeam()->getDepartement());
			$em = $this->getDoctrine()->getManager();
			$em->persist($transfer);
			
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
		
			// Send all notifications and save entities
			$em->flush();
		
			return $this->redirectToRoute('se_transfer_homepage');
		}
		
		return $this->render(
				'SETransferBundle:Transfer:addTransfer2.html.twig',
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
			$transfer->setDecision($this->get('security.context')->getToken()->getUser());
			$em->persist($transfer);
		
			$notif = new Notification();
			$usr = $this->get('security.context')->getToken()->getUser();
			$notif->setDateCreation(new \DateTime("now"));
			$notif->setSender($usr);
			$notif->setReceiver($transfer->getDemand());
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
			$transfer->setDecision($this->get('security.context')->getToken()->getUser());
			$em->persist($transfer);
		
			$notif = new Notification();
			$usr = $this->get('security.context')->getToken()->getUser();
			$notif->setDateCreation(new \DateTime("now"));
			$notif->setSender($usr);
			$notif->setReceiver($transfer->getDemand());
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
