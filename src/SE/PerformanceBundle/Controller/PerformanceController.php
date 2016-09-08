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
    	set_time_limit(300);//ini_set('max_execution_time', 300);
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
    	/*$listEmployees = $this->getDoctrine()
    		->getManager()
    		->getRepository('SEInputBundle:Employee')
    		->getAlphaCurrentEmployees()
    		;*/
    	
    	
    	$dates = array();
    	$date = new \DateTime();
    	$month = $date->format('m');
    	$date->setDate($date->format('Y'), $month, 1);
    	for ($i = 0; $month == $date->format('m') or $i >= 31; $i++){
    		$dates[$i] = clone $date;
    		$date->modify("+1 day");
    	}
    	
    	$employeeData = array();
    	foreach ($listEmployees as $e){
    		$data = array();
    		for ($i = 0; $i < sizeof($dates); $i++){
    			$day = array();
    			$date = $dates[$i];
    			$entries = $this->getDoctrine()
    				->getManager()
    				->getRepository('SEInputBundle:InputEntry')
    				->getEmployeeInputsAtDate($date->format('d'), $date->format('m'),
    						$date->format('y'), $e->getId());
    			$TO = 0; $Prod = 0; $Worked = 0; $Overtime = 0;
    			foreach ($entries as $entry){
    				$TO += $entry->getTotalTo();
    				$Worked += $entry->getTotalWorked();
    				$Overtime += $entry->getTotalOvertime();
    			}
    			$day['TO Confirmed'] = $TO;
    			$day['Productivity'] = $Worked ? $TO/$Worked : "-";
    			$day['Worked'] = $Worked;
    			$day['Overtime'] = $Overtime;
    			$data[$i] = $day;
    		}
    		
    		$employeeData[$e->getId()] = $data;
    	}
    	
    	
    	return $this->render('SEPerformanceBundle:Performance:index.html.twig',
    					array('listEmployees' => $listEmployees,
    							'departement' => $dep,
    							'team' => $this->get('security.context')->getToken()->getUser()->getTeam(),
    							'employeeData' => $employeeData,
    							'dates' => $dates
    					));
    }

    public function menuAction()
	{
		return $this->render('SEPerformanceBundle:Performance:menu.html.twig');
	}
}
