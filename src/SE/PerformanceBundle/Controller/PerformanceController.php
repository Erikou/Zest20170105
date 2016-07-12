<?php

namespace SE\PerformanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PerformanceController extends Controller
{
    public function indexAction()
    {
    	$listEmployees = $this->getDoctrine()
    	->getManager()
    	->getRepository('SEInputBundle:Employee')
    	->getCurrentEmployees()
    	;
    	
    	return $this->render('SEPerformanceBundle:Performance:index.html.twig',
    					array('listEmployees' => $listEmployees,
    					));
    }

    public function menuAction()
	{
		return $this->render('SEPerformanceBundle:Performance:menu.html.twig');
	}
}
