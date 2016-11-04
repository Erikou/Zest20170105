<?php

namespace SE\ReportBundle\Refresher;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;
use Monolog\Logger;
use SE\ReportBundle\Entity\AttendanceData;
use SE\InputBundle\Entity\UserInput;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
class AttendanceRefresher
{
	protected $em;
	private $logger;

    public function __construct(EntityManager $em, Structurer $structurer, RouterInterface $router, Logger $logger)
    {
        $this->logger = $logger;
    
        $this->em = $em;
        $this->structurer = $structurer;
        $this->router = $router;
    }

	public function updateData($year, $month, $days)
	{ 
	    $jsonAttendance = $this->structurer->getAttendanceStructure($year, $month, sizeof($days));
	    $jsonAttendance = $this->fillData($jsonAttendance, $month, $year);

	    $format = $this->formatData($jsonAttendance, $days, $month, $year);
	    $template = $format['template'];
	    $jsonAttendance = $format['jsonAttendance'];

	    $jsonData = $this->getTotalData($jsonAttendance, $days, $month, $year);

		$res = $this->saveData($year, $month, $jsonAttendance, $jsonData, $template);
		
		$response = array(
						  "template" => $template,
						  "jsonData" => $jsonData
						);
	    
	    return $response; 
	}

	public function fillData($jsonAttendance, $month, $year){

		//TODO : Maybe faster with query on inputEntries directly
		$userInputs = $this->em->getRepository('SEInputBundle:UserInput')->getMonthInputs($month,$year);
   		
		foreach ($userInputs as $userInput) {
			$dateInput = $userInput->getDateInput()->format("j");
			//when details of activities in tooltip -> team+shift required here
			foreach ($userInput->getInputEntries() as $inputEntry) {
				$regtohr = 0;
				$ottohr = 0;
				$employeeId = $inputEntry->getEmployee()->getMasterId();
				if (array_key_exists($employeeId, $jsonAttendance)) {
					if($inputEntry->getPresent() == 1){
						/////////////////////////////////UPDATE 25/02/16
						// From 01/02/16 onwards, TRANSFER OUT hours are removed from inputentry->totalhours directly in the Entity->userInput->computeHours() function
						// no need to remove them here anymore.
						//update hours
						if($inputEntry->getActivityHours()){
							foreach ($inputEntry->getActivityHours() as $activityHour) {
							 	if($activityHour->getActivity()->getId() == 13){
							 		$regtohr = $activityHour->getRegularHours();
							 		$ottohr = $activityHour->getOtHours();
							 		break;
							 	}
							}
						}
						//update hours
						$jsonAttendance[$employeeId][$dateInput]['presence'] = 1;
						$jsonAttendance[$employeeId][$dateInput]['absence'] = 0;
						$jsonAttendance[$employeeId][$dateInput]['halfday'] = $inputEntry->getHalfday();

						$jsonAttendance[$employeeId][$dateInput]['othr'] += $inputEntry->getTotalOvertime() - $ottohr;
						$jsonAttendance[$employeeId][$dateInput]['reghr'] += $inputEntry->getTotalHours() - $inputEntry->getTotalOvertime() - $regtohr;
						$jsonAttendance[$employeeId][$dateInput]['tothr'] += $inputEntry->getTotalHours() - $ottohr - $regtohr;
					}else{
						$jsonAttendance[$employeeId][$dateInput]['presence'] = 0;
						//TODO : Update when halfdays will be managed
						$jsonAttendance[$employeeId][$dateInput]['absence'] = $inputEntry->getAbsenceReason()->getName();
					}
				}
			}
		}
	
		return $jsonAttendance;
	}

	public function formatData($att, $days, $month, $year){

		$template = array();
		$templateRow = array();
		$today = new \DateTime();
		$today->modify( '-1 day' );
		$today->format( 'Y-m-d' );
    	
    	//TODO : Use foreach like in gettotal data for employees
		foreach ($att as $j => $e) {
   			if(isset($e)){
		 		unset($templateRow);
		 		$templateRow = array();  
		 		$templateRow[] = $e['employee'];
		 		$templateRow[] = $e['team'];
		 		$templateRow[] = $e['shift'];
			 	foreach ($days as $i => $day) {
   					$i++; //offset is 1->dayNb on jsonAttendance
					$e['total'] += $e[$i]['tothr'];
					$e['overtime'] += $e[$i]['othr'];
					//test to identify errors in hours reported
					if($e[$i]['presence'] == 0){
						if(isset($e[$i]['absence']) && $e[$i]['absence'] != "0"){//leave
							$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-leave'><i class='glyphicon glyphicon-remove'> </i> <strong>".$e[$i]['absence']."</strong></div>";
						}elseif($day['isWeekday'] && !$day['isHoliday'] && $day['dt'] < $today){//forgotten
							$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-missing'><i class='glyphicon glyphicon-question-sign'> </i></div>";	
						}else{//not working
							$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-absent'></div>";	
						}
					}elseif( ( $e[$i]['tothr'] > 11 || $e[$i]['othr'] > 11 || $e[$i]['reghr'] > 8 ) || ( $e[$i]['halfday'] == 1  && $e[$i]['reghr'] > 4 ) ){//warning too much hour
						$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-high'><i class='glyphicon glyphicon-exclamation-sign'> </i> ".$e[$i]['tothr']."</div>";
					}elseif( ( $e[$i]['reghr'] < 8 && $day['isWeekday'] && !$day['isHoliday']) || $e[$i]['tothr'] == 0 ){//missing hour
						if( $e[$i]['halfday'] == 0 || ( $e[$i]['halfday'] == 1  && $e[$i]['reghr'] < 4 ) ){ //really missing hour
							$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-low'><i class='glyphicon glyphicon-exclamation-sign'> </i> ".$e[$i]['tothr']."</div>";					
						}else{ //halfday
							$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-half'><i class='glyphicon glyphicon-adjust'> </i> ".$e[$i]['tothr']."</div>";
						}
					}else{//ok
						$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-ok'><i class='glyphicon glyphicon-ok'> </i> ".$e[$i]['tothr']."</div>";					
					}
					$templateRow[] = $dCell;
				}
				$templateRow[] = $e['total'];
				$templateRow[] = $e['overtime'];
				if($e['total'] > 0){ //if no hours during the month, do not include in table
					$template[] = $templateRow;
				}
			}
		}

		return array('jsonAttendance' => $att, 'template' => $template);
	}

	public function getTotalData($att, $days, $month, $year){

		$data = $this->structurer->getAttendanceReportStructure($year, $month, sizeof($days));
		
   		foreach ($days as $d => $day) {
   			$d++; //offset is 1->dayNb on jsonAttendance
   			foreach ($att as $id => $e) {
   				if(isset($e) && isset($e[$d])){
   					$data = $this->setTotalData($data, 0, 0, $d, $day, $id, $e);
   					$data = $this->setTotalData($data, $e['teamId'], 0, $d, $day, $id, $e);
   					$data = $this->setTotalData($data, $e['teamId'], $e['shift'], $d, $day, $id, $e);
   				}
   			}
   		}

   		$data = $this->structurer->forgetAttendanceDataKeys($data);

		return $data;					
	}

	public function setTotalData($data, $t, $s, $d, $day, $id, $e){

		//presence
		if( $e[$d]['presence'] == 1 && $e[$d]['tothr'] > 0 ){
			$data[$t][$s]['report']['presence'] += 1;
			$data[$t][$s]['attrate']['temp'][$d]['pres'] += 1;
		}

		//headcount
		if( ( $e[$d]['presence'] == 1  && $e[$d]['tothr'] > 0 ) || ( $e[$d]['presence'] == 0  && isset($e[$d]['absence']) && $e[$d]['absence'] != "0" ) ){
			$data[$t][$s]['report']['hc'] += 1;
			$data[$t][$s]['attrate']['temp'][$d]['hc'] += 1;
		}

		//hours
		$data[$t][$s]['report']['totalreghr'] += $e[$d]['reghr'];
		$data[$t][$s]['report']['totalothr'] += $e[$d]['othr'];
		$data[$t][$s]['report']['totalhr'] += $e[$d]['tothr'];
		if ( $day['isWeekday'] && !$day['isHoliday'] ){$data[$t][$s]['report']['wdot'] += $e[$d]['othr'];}
		else{$data[$t][$s]['report']['weot'] += $e[$d]['othr'];}

		//overtime
		//if( !isset($data[$t][$s]['otconso']['data'][$d]) || $data[$t][$s]['otconso']['data'][$d] == 0){$data[$t][$s]['otconso']['data'][$d] = $e[$d]['othr'];}
		//else{ 
		$data[$t][$s]['otconso']['data'][$d]['y'] += $e[$d]['othr'];
		$data[$t][$s]['otconso']['data'][$d]['tip'][1] = $e[$d]['othr'] > 0 ? ($data[$t][$s]['otconso']['data'][$d]['tip'][1] + 1) : $data[$t][$s]['otconso']['data'][$d]['tip'][1];
		$data[$t][$s]['otconso']['data'][$d]['tip'][3] += $e[$d]['tothr'];//temp data
		$data[$t][$s]['otconso']['data'][$d]['tip'][2] = $data[$t][$s]['otconso']['data'][$d]['tip'][3] > 0 ? (100 * round( $data[$t][$s]['otconso']['data'][$d]['y'] / $data[$t][$s]['otconso']['data'][$d]['tip'][3] , 2)) : 0;
		//}
		$data[$t][$s]['dailyot']['data'][$day['dw']] += $e[$d]['othr'];

		//daily+monthly rates
		if(	$data[$t][$s]['report']['hc'] > 0){ $data[$t][$s]['report']['attrate'] = 100 * round( $data[$t][$s]['report']['presence'] / $data[$t][$s]['report']['hc'] , 2 );}   		
		if(	$data[$t][$s]['attrate']['temp'][$d]['hc'] > 0){ $data[$t][$s]['attrate']['data'][$d] = 100 * round( ($data[$t][$s]['attrate']['temp'][$d]['pres'] / $data[$t][$s]['attrate']['temp'][$d]['hc']) , 2 ); }
		else{ $data[$t][$s]['attrate']['data'][$d] = 0;}
		
		return $data;					
	}

	public function saveData($year, $month, $jsonAttendance, $jsonData, $template)
	{ 
		//TODO : error handler
		$data = $this->em->getRepository('SEReportBundle:AttendanceData')->findOneBy(array('year' => $year, 'month' => $month));

		if($data){
			$data->setJsonAttendance(json_encode($jsonAttendance));
			$data->setJsonData(json_encode($jsonData));
			$data->setTableTemplate(json_encode($template));
			$data->setRefresher(0);
			
		}else{
			$newData = new AttendanceData();

			$newData->setMonth($month);
			$newData->setYear($year);
			$newData->setJsonAttendance(json_encode($jsonAttendance));
			$newData->setJsonData(json_encode($jsonData));
			$newData->setTableTemplate(json_encode($template));

			$this->em->persist($newData);
		}
		
		$this->em->flush();

		return true;
	}

	public function getActivityDetails($y, $m, $d, $e){

	    $is = $this->em->getRepository('SEInputBundle:InputEntry')->getEmployeeInputsAtDate($d,$m,$y,$e);
	    $det = array('totot' => 0, 'totreg' => 0, 'tothr' => 0, 'to' => 0, 'res' => 0, 'whr' => 0);
	    $j = 0;
	    $transfer =false;

	    foreach ($is as $i) {
			$regtohr = 0;
			$ottohr = 0;
	    	$det['name'] = $i->getEmployee()->getName();
	    	$det['date'] = $i->getUserInput()->getDateInput()->format('d-m-Y');
	    	$det['tab'][$j]['present'] = $i->getPresent();
	    	if( !$det['tab'][$j]['present'] ){ $det['tab'][$j]['absence'] = $i->getAbsenceReason()->getName(); }
	    	$det['tab'][$j]['link'] = $this->router->generate('se_input_review_details', array('id' => $i->getUserInput()->getId()));
	    	if( $i->getActivityHours() ){
	    		foreach ($i->getActivityHours() as $a) {
	    			if($a->getActivity()->getId() == 13){
				 		$regtohr = $a->getRegularHours();
				 		$ottohr = $a->getOtHours();
				 		$transfer = true;
					}else{
						$det['tab'][$j]['row'][] = "<tr><td>".$a->getActivity()->getName()."</td><td>".$a->getRegularHours()."</td><td>".$a->getOtHours()."</td></tr>";
					}	
		    	}
		    }
		    $det['tab'][$j]['header'] = $transfer ? "<i class='glyphicon glyphicon-log-out text-danger' data-toggle='tooltip' data-placement='top' title='Transfer Out'> </i> ".$i->getUserInput()->getTeam()->getName()." - Shift ".$i->getUserInput()->getShift()->getId()." - ".$i->getSesa() : $i->getUserInput()->getTeam()->getName()." - Shift ".$i->getUserInput()->getShift()->getId()." - ".$i->getSesa();
	    	$det['totot'] += $i->getTotalOvertime() - $ottohr;
	    	$det['totreg'] += $i->getTotalHours() - $i->getTotalOvertime() - $regtohr;;
	    	$det['tothr'] += $i->getTotalHours() - $ottohr - $regtohr;
	    	$det['to'] += $i->getTotalTo();
	    	$det['whr'] += $i->getTotalWorkingHours();
	    	if ( $det['whr'] != 0 ){ $det['res'] = round( $det['to'] / $det['whr'] , 2); }
	    	$j++;
	    }

		return $det;
	}
}
