<?php

namespace SE\PerformanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\Employee;
use SE\InputBundle\Entity\Activity;

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
    	$team = $this->get('security.context')->getToken()->getUser()->getTeam();
    	$dep = $team ? $team->getDepartement() : null;
    	
    	if ($team == null){
    		$listEmployees = $this->getDoctrine()
    			->getManager()
    			->getRepository('SEInputBundle:Employee')
    			->getCurrentEmployees();// in case user doesn't have a team (admin for example)
    	} else {
    		// Get concerned employees
    		/*$listEmployees = $this->getDoctrine()
    			->getManager()
    			->getRepository('SEInputBundle:Employee')
    			->getDepartementEmployees($dep->getId()); // doesn't work ? uh ?*/
    		$listtmp = $this->getDoctrine()
    			->getManager()
    			->getRepository('SEInputBundle:Employee')
    			->getCurrentEmployees();
    		$listEmployees = array();
    		foreach ($listtmp as $e){
    			if ($e->getDefaultTeam() == $team){
    				$listEmployees[] = $e;
    			}
    		}
    	}
    	
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
    				->getEmployeeInputsAtDate2($date, $e->getId());
				
    			$TO = 0; $Worked = 0; $Overtime = 0; $Productivity = 0;
    			foreach ($entries as $entry){
    				$TO += $entry->getTotalTo();
    				$Worked += $entry->getTotalWorkingHours();
    				$Overtime += $entry->getTotalOvertime();
    				$Productivity += $entry->getTotalProd();
    			}
				
    			$day['TO Confirmed'] = $TO;
    			$day['Productivity'] = $Productivity;
    			$day['Worked'] = $Worked;
    			$day['Overtime'] = $Overtime;
    			$data[$i] = $day;
    		}
    		
    		$employeeData[$e->getId()] = $data;
    	}
    	
    	
    	return $this->render('SEPerformanceBundle:Performance:index.html.twig',
    					array('listEmployees' => $listEmployees,
    							'departement' => $dep,
    							'team' => $team,
    							'employeeData' => $employeeData,
    							'dates' => $dates
    					));
    }

    public function detailAction($employee_id){
		$em = $this->getDoctrine()->getManager();
		$emp = $em->find(Employee::class, $employee_id);
    	
    	if ($emp == null){
    		return $this->redirectToRoute('se_performance_homepage');
    	}
    	
    	$dates = array();
    	$date = new \DateTime();
    	$month = $date->format('m');
    	$date->setDate($date->format('Y'), $month, 1);
    	for ($i = 0; $month == $date->format('m') or $i >= 31; $i++){
    		$dates[$i] = clone $date;
    		$date->modify("+1 day");
    	}
    	
    	$activities = array();
    	$allActivities = $this->getDoctrine()->getRepository(Activity::class)->findAll();
    	foreach ($allActivities as $a){
    		if ($a->getId() != 11/* && $a->getId() != 13*/)
    			$activities[$a->getName()] = 0;
    	}
    	
    	$month = array();
    	$data = array('TO' => 0, 'Hours' => 0, 'Regular' => 0,
    			'Overtime' => 0, 'Absent' => 0, 'Training' => 0, 'Transfer' => 0);
    	for ($i = 0; $i < sizeof($dates); $i++){
    		$day = array();
    		$date = $dates[$i];
    		$entries = $this->getDoctrine()
    			->getManager()
    			->getRepository('SEInputBundle:InputEntry')
    			->getEmployeeInputsAtDate2($date, $employee_id);
			
    		$TO = 0; $Worked = 0; $Overtime = 0; $Productivity = 0;
    		foreach ($entries as $entry){
    			$TO += $entry->getTotalTo();
    			$Worked += $entry->getTotalWorkingHours();
    			$Overtime += $entry->getTotalOvertime();
    			$Productivity += $entry->getTotalProd();
    			if ($entry->getPresent() == 0)
    				$data['Absent']++;
    			foreach ($entry->getActivityHours() as $ah){
    				if ($ah->getActivity()->getId() != 11 && $ah->getActivity()->getId() != 13)
    					$activities[$ah->getActivity()->getName()] += $ah->getRegularHours() + $ah->getOtHours();
    				else if ($ah->getActivity()->getId() == 13) // TODO: check Transfer activity
    					$activities[$ah->getActivity()->getName()] += $ah->getRegularHours() + $ah->getOtHours();
    			}
    		}
    		$data['TO'] += $TO;
    		$data['Hours'] += $Worked + $Overtime;
    		$data['Regular'] += $Worked;
    		$data['Overtime'] += $Overtime;
			
    		$day['TO Confirmed'] = $TO;
    		$day['Productivity'] = $Productivity;
    		$day['Worked'] = $Worked;
    		$day['Overtime'] = $Overtime;
    		$month[$i] = $day;
    	}

    	return $this->render('SEPerformanceBundle:Performance:detail.html.twig',
    			array('employee' => $emp,
    					'month' => $month,
    					'data' => $data,
    					'dates' => $dates,
    					'activities' => $activities,
    			));
    }
    
    public function menuAction()
	{
		return $this->render('SEPerformanceBundle:Performance:menu.html.twig');
	}
}
