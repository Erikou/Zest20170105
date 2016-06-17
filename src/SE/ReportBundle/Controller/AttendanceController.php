<?php

namespace SE\ReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SE\ReportBundle\Entity\AttendanceData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class AttendanceController extends Controller
{
	public function hrAction()
	{ 
	    $em = $this->getDoctrine()->getManager();
   		$year = date('Y');
   		$month = date('n');
   		$calendar = $em->getRepository('SEReportBundle:Calendar')->getMonth($month, $year);
	   	$tm = $em->getRepository('SEInputBundle:Team')->getCurrentTeams();

	    return $this->render('SEReportBundle:Attendance:hr.html.twig', array(
    		'calendar' => $calendar,
    		'tm' => $tm
    	)); 
	}

	public function attendanceAction()
	{ 
	    $em = $this->getDoctrine()->getManager();
	    $request = $this->get('request');        
	    $year = $request->get('year');
	    $month = $request->get('month');
	    $days = $em->getRepository('SEReportBundle:Calendar')->getMonthArray($month, $year);

	    /////////////////////////LA SUPER FONCTION////////////////////////////
		//(sérieux ?)
	    $data = $em->getRepository('SEReportBundle:AttendanceData')->findOneBy(array('year' => $year, 'month' => $month));
	    if(!$data || $data->getRefresher()){
	    	//need to refresh -> go to refresher Controller
	    	$refresher = $this->get('se_report.refresher.attendance');
	    	$response = $refresher->updateData($year, $month, $days);
	    }else{
	    	//get the data and send them
	    	//$jsonAttendance = json_decode($data->getJsonAttendance());
	    	$template = json_decode($data->getTableTemplate());
	    	$jsonData = json_decode($data->getJsonData());

			$response = array(
				  //"jsonAttendance" => $jsonAttendance, // NO NEED
				  "template" => $template,
				  "jsonData" => $jsonData
				);
	    }

	    $tm = $em->getRepository('SEInputBundle:Team')->getHistoricalTeams($year,$month);
		$response['filters'] = $this->render('SEReportBundle:Utilities:filters.html.twig', array('tm' => $tm))->getContent();
		
		$headers = array();
		foreach ($days as $d) {$headers[$d['d']][] = array('id' => $d['d']."/".$d['m'], 'wd' => $d['isWeekday'], 'hd' => $d['isHoliday']);}
		$response['headers'] = $headers;
		$response['daysNb'] = sizeof($days); 

	    return new Response(json_encode($response)); 
	}

	public function activitiesDetailsAction()
	{ 
		//$em = $this->getDoctrine()->getManager();
	    $request = $this->get('request');        
	    $y = $request->get('y');
	    $m = $request->get('m');
	    $d = $request->get('d');
	    $e = $request->get('e');

	    $refresher = $this->get('se_report.refresher.attendance');
	    $response = array("det" => $refresher->getActivityDetails($y, $m, $d, $e));
	    
	    return new Response(json_encode($response)); 
	}
}
