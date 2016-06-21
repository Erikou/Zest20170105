<?php

namespace SE\TransferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Form\UserInputType;
use Symfony\Component\HttpFoundation\Request;
use SE\InputBundle\Entity\Transfer;

class TransferController extends Controller
{
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
    	
      	$userInput = new UserInput();
    	$form = $this->createForm(new UserInputType(), $userInput);
    	
        return $this->render('SETransferBundle:Transfer:index.html.twig',
        		array('listEmployees' => $listEmployees,
        				'listTeams' => $listTeams,
        				'listTransfers' => $listTransfers,
        				'form' => $form->createView()
        		));
    }
    
    public function addTransferAction(Request $request)
	{
		// Build the form
		$transfer = new Transfer();
		$transfer->setValidated(false);
		$form = $this->createForm($this->get('inputBundle_addtransfer'), $transfer);
		
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
}
