<?php

namespace SE\PerformanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PerformanceController extends Controller
{
	private function getUsrDep(){
		$usr= $this->get('security.context')->getToken()->getUser();
		$usrTeam = $usr->getTeam();
		$usrDep = null;
		if ($usrTeam != null)
			$usrDep = $usrTeam->getDepartement();
		return $usrDep;
	}
	
    public function indexAction()
    {
    	$dep = $this->getUsrDep();
    	
    	// Get concerned employees
    	$listEmployees = $this->getDoctrine()
    	->getManager()
    	->getRepository('SEInputBundle:Employee')
    	->getDepartementEmployees($dep->getId())
    	; // doesn't work ? uh ?
    	
    	if ($dep == null){
    		$listEmployees = $this->getDoctrine()
    			->getManager()
    			->getRepository('SEInputBundle:Employee')
    			->getCurrentEmployees()
    			;// in case user doesn't have a team (admin for example)
    	}
    	$listEmployees = $this->getDoctrine()
    		->getManager()
    		->getRepository('SEInputBundle:Employee')
    		->getAlphaCurrentEmployees()
    		;
    	$employeeData = array();
    	foreach ($listEmployees as $e){
    		$data = array();
    		for ($i = 1; $i < 31; $i++){
    			$day = array();
    			$day['TO Confirmed'] = 100;
    			$day['Productivity'] = 100;
    			$day['Worked'] = 8;
    			$day['Overtime'] = 0;
    			$data[$i] = $day;
    		}
    		
    		$employeeData[$e->getId()] = $data;
    	}
    	
    	
    	return $this->render('SEPerformanceBundle:Performance:index.html.twig',
    					array('listEmployees' => $listEmployees,
    							'departement' => $dep,
    							'employeeData' => $employeeData,
    					));
    }

    public function menuAction()
	{
		return $this->render('SEPerformanceBundle:Performance:menu.html.twig');
	}
}
