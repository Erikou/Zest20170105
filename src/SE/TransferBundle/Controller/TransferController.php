<?php

namespace SE\TransferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TransferController extends Controller
{
    public function indexAction()
    {

    	$listEmployees = $this->getDoctrine()
      		->getManager()
      		->getRepository('SEInputBundle:Employee')
      		->getAlphaCurrentEmployees()
    	;
    	
        return $this->render('SETransferBundle:Transfer:index.html.twig', array('listEmployees' => $listEmployees));
    }

    public function menuAction()
	{
		return $this->render('SETransferBundle:Transfer:menu.html.twig');
	}
}
