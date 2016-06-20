<?php

namespace SE\TransferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Form\UserInputType;
use Symfony\Component\HttpFoundation\Request;

class TransferController extends Controller
{
    public function indexAction(Request $request)
    {

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
        				'form' => $form->createView()
        		));
    }

    public function menuAction()
	{
		return $this->render('SETransferBundle:Transfer:menu.html.twig');
	}
}
