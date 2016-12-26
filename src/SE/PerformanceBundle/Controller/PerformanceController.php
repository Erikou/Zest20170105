<?php

namespace SE\PerformanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\Employee;
use SE\InputBundle\Entity\Activity;
use Symfony\Component\HttpFoundation\Request;

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
	
    public function indexAction(Request $request)
    {
    	return $this->globalMonthAction($request, date("m"), date("Y"));
    }
	
    public function globalMonthAction(Request $request, $month, $year)
    {
    	set_time_limit(300);//ini_set('max_execution_time', 300);
    	
    	$message = "";

	    // Handle the submit (will only happen on POST)
		if ($request->getMethod() == 'POST') {
	    	$request_year = $request->request->get('year-picker');
	    	$request_month = $request->request->get('month-picker');
	    	if ($request_year != null && $request_year > 2014)
				return $this->redirectToRoute('se_performance_global_month', array('year' => $request_year, 'month' => $request_month));
	    	else
	    		$message = "Please pick a year from 2015 and later";
		}
    	
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
    	$date->setDate($year, $month, 1);
    	for ($i = 0; $month == $date->format('m'); $i++){
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
    							'dates' => $dates,
    							'message' => $message,
    							'month' => $month,
    							'year' => $year
    					));
    }

    public function globalYearAction(Request $request, $year)
    {
    	set_time_limit(300);//ini_set('max_execution_time', 300);
    	
    	$message = "";

	    // Handle the submit (will only happen on POST)
		if ($request->getMethod() == 'POST') {
	    	$request_year = $request->request->get('year-picker');
	    	if ($request_year != null && $request_year > 2014)
				return $this->redirectToRoute('se_performance_global_year', array('year' => $request_year));
	    	else
	    		$message = "Please pick a year from 2015 and later";
		}
    	
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
    	$date->setDate($year, 1, 1);
    	for ($i = 0; $year == $date->format('Y'); $i++){
    		$dates[$i] = clone $date;
    		$date->modify("+7 day");
    	}
    	
    	$employeeData = array();
    	foreach ($listEmployees as $e){
    		$data = array();
    		for ($i = 0; $i < sizeof($dates); $i++){
    			$week = array();
	    		$week['TO Confirmed'] = 0;
	    		$week['Productivity'] = 0;
	    		$week['Worked'] = 0;
	    		$week['Overtime'] = 0;
	    			
    			$date = $dates[$i];
    			for ($i = 0; $i < 7; $i++){
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
					
	    			$week['TO Confirmed'] += $TO;
	    			$week['Productivity'] += $Productivity;
	    			$week['Worked'] += $Worked;
	    			$week['Overtime'] += $Overtime;
	    			$date->modify("+1 day");
    			}
    			$data[$i] = $week;
    		}
    		
    		$employeeData[$e->getId()] = $data;
    	}
    	
    	
    	return $this->render('SEPerformanceBundle:Performance:global_year.html.twig',
    					array('listEmployees' => $listEmployees,
    							'departement' => $dep,
    							'team' => $team,
    							'employeeData' => $employeeData,
    							'dates' => $dates,
    							'message' => $message,
    							'year' => $year
    					));
    }
    
    public function detailAction(Request $request, $employee_id){
    	return $this->employeeMonthAction($request, $employee_id, date("m"), date("Y"));
    }

    public function employeeMonthAction(Request $request, $employee_id, $month, $year){
    	set_time_limit(300);//ini_set('max_execution_time', 300);
    	
    	$message = "";

	    // Handle the submit (will only happen on POST)
		if ($request->getMethod() == 'POST') {
	    	$request_year = $request->request->get('year-picker');
	    	$request_month = $request->request->get('month-picker');
	    	if ($request_year != null && $request_year > 2014)
				return $this->redirectToRoute('se_performance_detail_month', array('employee_id' => $employee_id, 'year' => $request_year, 'month' => $request_month));
	    	else
	    		$message = "Please pick a year from 2015 and later";
		}
    	 
    	$em = $this->getDoctrine()->getManager();
    	$emp = $em->find(Employee::class, $employee_id);
    	 
    	if ($emp == null){
    		return $this->redirectToRoute('se_performance_homepage');
    	}
    	 
    	$dates = array();
    	$date = new \DateTime();
    	$date->setDate($year, $month, 1);
    	for ($i = 0; $month == $date->format('m'); $i++){
    		$dates[$i] = clone $date;
    		$date->modify("+1 day");
    	}
    	 
    	$activityHoursDone = array();
    	$activityTODone = array();
    	$allActivities = $this->getDoctrine()->getRepository(Activity::class)->findAll();
    	foreach ($allActivities as $a){
    		if ($a->getId() != 11/* && $a->getId() != 13*/)
    			$activityHoursDone[$a->getName()] = 0;
    			$activityTODone[$a->getName()] = 0;
    	}
    	
    	$weeklyOvertime = [0, 0, 0, 0, 0, 0, 0]; //OT per day of the week, starting sunday
    	$weekday = array();
    	$weekday["Sunday"] = 0; $weekday["Monday"] = 1; $weekday["Tuesday"] = 2; $weekday["Wednesday"] = 3;
    	$weekday["Thursday"] = 4; $weekday["Friday"] = 5; $weekday["Saturday"] = 6;
    	 
    	$monthData = array();
    	$monthData['TO Confirmed'] = array();
    	$monthData['Productivity'] = array();
    	$monthData['Worked'] = array();
    	$monthData['Overtime'] = array();
    	// Time stats for the full month
    	
    	$data = array('TO' => 0, 'Hours' => 0, 'Regular' => 0,
    			'Overtime' => 0, 'Absent' => 0, 'Training' => 0, 'Transfer' => 0);
    	for ($i = 0; $i < sizeof($dates); $i++){
    		$date = $dates[$i];
    		$entries = $this->getDoctrine()
    		->getManager()
    		->getRepository('SEInputBundle:InputEntry')
    		->getEmployeeInputsAtDate2($date, $employee_id);
    			
    		$TO = 0; $Worked = 0; $Overtime = 0; $Productivity = 0;
    		foreach ($entries as $entry){ // division by inputentry
    			$TO += $entry->getTotalTo();
    			$Worked += $entry->getTotalWorkingHours();
    			$Overtime += $entry->getTotalOvertime();
    			$Productivity += $entry->getTotalProd();
    			
    			if ($entry->getPresent() == 0)
    				$data['Absent']++;

	    		foreach ($entry->getActivityHours() as $ah){ // divide inputentry into activityhours
	    			if ($ah->getActivity()->getId() != 11 && $ah->getActivity()->getId() != 13){
	    				$activityHoursDone[$ah->getActivity()->getName()] += $ah->getRegularHours() + $ah->getOtHours();
	    				$activityProportion = $activityHoursDone[$ah->getActivity()->getName()] / ($Worked + $Overtime);
	    				$activityTODone[$ah->getActivity()->getName()] += $entry->getTotalTo() * $activityProportion;
	    			} else if ($ah->getActivity()->getId() == 13){ // TODO: check Transfer activity
	    				$activityHoursDone[$ah->getActivity()->getName()] += $ah->getRegularHours() + $ah->getOtHours();
	    			}
	    		}
    		}
    		$data['TO'] += $TO;
    		$data['Hours'] += $Worked + $Overtime;
    		$data['Regular'] += $Worked;
    		$data['Overtime'] += $Overtime;
    			
    		$monthData['TO Confirmed'][] = $TO;
    		$monthData['Productivity'][] = $Productivity;
    		$monthData['Worked'][] = $Worked;
    		$monthData['Overtime'][] = $Overtime;
    		
    		
    		$weeklyOvertime[$weekday[$date->format('l')]] += $Overtime;
    	}
    	
    	$activityTime = array();
    	$activityName = array();
    	$activityKe = array();
    	foreach ($allActivities as $a){
    		if ($a->getId() != 11/* && $a->getId() != 13*/)
    			$activityTime[] = $activityHoursDone[$a->getName()];
    			$activityName[] = $a->getName();
    			if ($a->getDefaultTarget() && $activityHoursDone[$a->getName()])
    				$activityKe[] = $activityTODone[$a->getName()] / ($activityHoursDone[$a->getName()] * $a->getDefaultTarget());
    			else
    				$activityKe[] = 0;
    	}
    
    	return $this->render('SEPerformanceBundle:Performance:month.html.twig',
    			array('employee' => $emp,
    					'month' => $month,
    					'year' => $year,
    					'data' => $data,
    					'dates' => $dates,
    					'monthData' => $monthData,
    					'activityName' => $activityName,
    					'activityTime' => $activityTime,
    					'activityKe' => $activityKe,
    					'weeklyOvertime' => $weeklyOvertime,
    					'message' => $message,
    			));
    }
    
    public function employeeYearAction(Request $request, $employee_id, $year){

    	set_time_limit(300);//ini_set('max_execution_time', 300);
    	
    	$message = "";

	    // Handle the submit (will only happen on POST)
		if ($request->getMethod() == 'POST') {
	    	$request_year = $request->request->get('year-picker');
	    	if ($request_year != null && $request_year > 2014)
				return $this->redirectToRoute('se_performance_detail_year', array('employee_id' => $employee_id, 'year' => $request_year));
	    	else
	    		$message = "Please pick a year from 2015 and later";
		}
    	
    	$em = $this->getDoctrine()->getManager();
    	$emp = $em->find(Employee::class, $employee_id);
    	
    	if ($emp == null){
    		return $this->redirectToRoute('se_performance_homepage');
    	}
    	
    	$dates = array();
    	$date = new \DateTime();
    	$date->setDate($year, 1, 1);
    	for ($i = 0; $year == $date->format('Y'); $i++){
    		$dates[$i] = clone $date;
    		$date->modify("+7 day");
    	}
    	
    	$activityHoursDone = array();
    	$activityTODone = array();
    	$allActivities = $this->getDoctrine()->getRepository(Activity::class)->findAll();
    	foreach ($allActivities as $a){
    		if ($a->getId() != 11/* && $a->getId() != 13*/)
    			$activityHoursDone[$a->getName()] = 0;
    			$activityTODone[$a->getName()] = 0;
    	}
    	 
    	$weeklyOvertime = [0, 0, 0, 0, 0, 0, 0]; //OT per day of the week, starting sunday
    	$weekday = array();
    	$weekday["Sunday"] = 0; $weekday["Monday"] = 1; $weekday["Tuesday"] = 2; $weekday["Wednesday"] = 3;
    	$weekday["Thursday"] = 4; $weekday["Friday"] = 5; $weekday["Saturday"] = 6;
    	
    	$yearData = array();
    	$yearData['TO Confirmed'] = array();
    	$yearData['Productivity'] = array();
    	$yearData['Worked'] = array();
    	$yearData['Overtime'] = array();
    	// Time stats for the full month
    	 
    	$data = array('TO' => 0, 'Hours' => 0, 'Regular' => 0,
    			'Overtime' => 0, 'Absent' => 0, 'Training' => 0, 'Transfer' => 0);
    	for ($i = 0; $i < sizeof($dates); $i++){
    		$date = $dates[$i];
    		
    		$week_TO = 0;
    		$week_Prod = 0;
    		$week_Worked = 0;
    		$week_OT = 0;
    		
    		for ($d = 0; $d < 7; $d++){
	    		$entries = $this->getDoctrine()
	    		->getManager()
	    		->getRepository('SEInputBundle:InputEntry')
	    		->getEmployeeInputsAtDate2($date, $employee_id);
	    		 
	    		$TO = 0; $Worked = 0; $Overtime = 0; $Productivity = 0;
	    		foreach ($entries as $entry){ // division by inputentry
	    			$TO += $entry->getTotalTo();
	    			$Worked += $entry->getTotalWorkingHours();
	    			$Overtime += $entry->getTotalOvertime();
	    			$Productivity += $entry->getTotalProd();
	    			 
	    			if ($entry->getPresent() == 0)
	    				$data['Absent']++;

	    			foreach ($entry->getActivityHours() as $ah){ // divide inputentry into activityhours
	    				if ($ah->getActivity()->getId() != 11 && $ah->getActivity()->getId() != 13){
	    					$activityHoursDone[$ah->getActivity()->getName()] += $ah->getRegularHours() + $ah->getOtHours();
	    					$activityProportion = $activityHoursDone[$ah->getActivity()->getName()] / ($Worked + $Overtime);
	    					$activityTODone[$ah->getActivity()->getName()] += $entry->getTotalTo() * $activityProportion;
	    				} else if ($ah->getActivity()->getId() == 13){ // TODO: check Transfer activity
	    					$activityHoursDone[$ah->getActivity()->getName()] += $ah->getRegularHours() + $ah->getOtHours();
	    				}
	    			}
	    	
	    		}
	    		$data['TO'] += $TO;
	    		$data['Hours'] += $Worked + $Overtime;
	    		$data['Regular'] += $Worked;
	    		$data['Overtime'] += $Overtime;	  
    		
	    		$week_TO += $TO;
	    		$week_Prod += $Productivity;
	    		$week_Worked += $Worked;
	    		$week_OT += $Overtime;  	
	    	
	    		$weeklyOvertime[$weekday[$date->format('l')]] += $Overtime;
    			
	    		$date->modify("+1 day");
    		}
	    		 
	    	$yearData['TO Confirmed'][] = $week_TO;
	    	$yearData['Productivity'][] = $week_Prod;
	    	$yearData['Worked'][] = $week_Worked;
	    	$yearData['Overtime'][] = $week_OT;
    	}
    	 
    	$activityTime = array();
    	$activityName = array();
    	$activityKe = array();
    	foreach ($allActivities as $a){
    		if ($a->getId() != 11/* && $a->getId() != 13*/)
    			$activityTime[] = $activityHoursDone[$a->getName()];
    			$activityName[] = $a->getName();
    			if ($a->getDefaultTarget() && $activityHoursDone[$a->getName()])
    				$activityKe[] = $activityTODone[$a->getName()] / ($activityHoursDone[$a->getName()] * $a->getDefaultTarget());
    				else
    					$activityKe[] = 0;
    	}
    	
    	return $this->render('SEPerformanceBundle:Performance:year.html.twig',
    			array('employee' => $emp,
    					'yearData' => $yearData,
    					'year' => $year,
    					'data' => $data,
    					'dates' => $dates,
    					'activityName' => $activityName,
    					'activityTime' => $activityTime,
    					'activityKe' => $activityKe,
    					'weeklyOvertime' => $weeklyOvertime,
    					'message' => $message
    			));
    }
    
    public function menuAction()
	{
		return $this->render('SEPerformanceBundle:Performance:menu.html.twig');
	}
}
