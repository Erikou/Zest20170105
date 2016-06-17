<?php

namespace SE\InputBundle\Controller;

use SE\InputBundle\Entity\Employee;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Entity\InputEntry;
use SE\InputBundle\Entity\EditorEntry;
use SE\InputBundle\Form\EditorEntryType;
use SE\InputBundle\Form\UserInputType;
use SE\InputBundle\Form\InputEntryType;
use SE\InputBundle\Entity\InputReview;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

class DashboardController extends Controller
{

  public function reviewAction()
    {
      $em = $this->getDoctrine()->getManager();
      //select errors
      $importErrors = $em->getRepository('SEInputBundle:InputReview')->getLastMonthImportErrors();
      $inputErrors = $em->getRepository('SEInputBundle:InputReview')->getLastMonthMissingInputErrors();
      $toErrors = $em->getRepository('SEInputBundle:InputReview')->getLastMonthToErrors();
      $hourErrors = $em->getRepository('SEInputBundle:InputReview')->getLastMonthHoursErrors();
      $userInputs = $em->getRepository('SEInputBundle:UserInput')->getLastMonth();
      $tm = $em->getRepository('SEInputBundle:Team')->getCurrentTeams();

      //check if input has been filled in the meantime, remove the error if so
      foreach ($inputErrors as $errorToCheck) {
        if($em->getRepository('SEInputBundle:UserInput')->findOneBy(array('dateInput' => $errorToCheck->getDate(), 'team' => $errorToCheck->getTeam(), 'shift' =>  $errorToCheck->getShift()))){
          $em->remove($errorToCheck);
          $em->flush();
        }
      }

      return $this->render('SEInputBundle:Dashboard:review.html.twig', array(
        'importErrors' => $importErrors,
        'inputErrors' => $inputErrors,
        'toErrors' => $toErrors,
        'hourErrors' => $hourErrors,
        'userInputs' => $userInputs,
        'tm' => $tm
      ));
    }

  public function ignoreAction()
  { 
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $idInput = $request->get('idInput');
    
    $ignoreInput = $em->getRepository('SEInputBundle:InputReview')->findOneBy(array('id' => $idInput));

    if ($ignoreInput){
    
      $ignoreInput->setStatus(1);
      $em->persist($ignoreInput);
      $em->flush();

      $response = array("code" => 100, "success" => true);
    }else{
      $response = array("code" => 400, "success" => false);
    }
    return new Response(json_encode($response)); 
  }

  /**
  * @Security("has_role('ROLE_INPUT_EDIT')")
  */
  public function deleteAction()
  { 
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $idInput = $request->get('idInput');
    
    $deleteInput = $em->getRepository('SEInputBundle:UserInput')->findOneBy(array('id' => $idInput));
    if($deleteInput){
      $deleteEntries = $em->getRepository('SEInputBundle:InputEntry')->findBy(array('user_input' => $idInput));
      if($deleteEntries){
        foreach ($deleteEntries as $deleteEntry) {
          $deleteActivityHours = $em->getRepository('SEInputBundle:ActivityHours')->findBy(array('input' => $deleteEntry));
          if ($deleteActivityHours) {
            foreach ($deleteActivityHours as $deleteActivityHour) {
              // un-record to lines too
              foreach ($em->getRepository('SEInputBundle:SAPRF')->getRecordedTo($deleteInput->getDateInput(), $deleteEntry->getSesa()) as $recordedTo) {
                $recordedTo->setRecorded(0);
              }
              $em->remove($deleteActivityHour);
            }
          }
          $em->remove($deleteEntry);
        }
      }
      
      //reset manual TO Lines

      $manualTOlines = $em->getRepository('SEInputBundle:SAPRF')->resetGeneralManualTo($deleteInput->getDateInput(), $deleteInput->getTeam()->getId());
      //time definition
      $otStart = $deleteInput->getOtStartTime();
      $otEnd = $deleteInput->getOtEndTime();
      $start = $deleteInput->getShift()->getStartTime();
      $end = $deleteInput->getShift()->getEndTime();
      $regularReverse = ($start < $end ? true : false);
      $otReverse = ($otStart > $otEnd ? true : false);

      if($manualTOlines){
        foreach ($manualTOlines as $manualToLine) {
          $timeConf = $manualToLine->getTimeConfirmation(); 
          //if inside right time interval + to line not already affected
          if($manualToLine->getRecorded() == 1){ 
            if( ( $regularReverse and ($timeConf <= $end) and ($timeConf >= $start) ) or ( !$regularReverse and ( ( $timeConf >= $start ) or ( $timeConf <= $end ) ) ) ) { //regular hours
              $manualToLine->setRecorded(null);  
            }
          }
        }
      }

      $em->remove($deleteInput);
      $em->flush();

      $response = array("code" => 100, "success" => true);

    }else{
      $response = array("code" => 400, "success" => false);
    }

    return new Response(json_encode($response)); 
  }

  public function timeReviewAction(){
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $year = $request->get('year');
    $month = $request->get('month');
  
    //select errors
    $importErrors = $em->getRepository('SEInputBundle:InputReview')->getMonthImportErrors($month, $year);
    $inputErrors = $em->getRepository('SEInputBundle:InputReview')->getMonthMissingInputErrors($month, $year);
    $toErrors = $em->getRepository('SEInputBundle:InputReview')->getMonthToErrors($month, $year);
    $hourErrors = $em->getRepository('SEInputBundle:InputReview')->getMonthHoursErrors($month, $year);
    $userInputs = $em->getRepository('SEInputBundle:UserInput')->getMonthInputs($month, $year);
    $tm = $em->getRepository('SEInputBundle:Team')->getHistoricalTeams($year,$month);
    $filters = $this->render('SEReportBundle:Utilities:filters.html.twig', array('tm' => $tm))->getContent();

    $hTemplate = array();
    $eTemplate = array();
    
    foreach ($userInputs as $u) {
      $hView = $this->render('SEInputBundle:Utilities:historyView.html.twig', array('input' => $u))->getContent();
      $hTemplate[] = array($u->getDateInput()->format('d-m-Y'),
                           $u->getTeam()->getName(),
                           $u->getShift()->getId(),
                           $u->getTotalHoursInput(),
                           $u->getTotalWorkingHoursInput(),
                           $u->getTotalHeadcount(),
                           $u->getTotalToInput(),
                           $u->getTotalProdInput(),
                           $hView);
    }

    foreach ($inputErrors as $i) {
      $eView = $this->render('SEInputBundle:Utilities:errorsView.html.twig', array('input' => $i))->getContent();
      $eTemplate[] = array($i->getDate()->format('d-m-Y'),$i->getTeam()->getName(),$i->getShift()->getId(),$eView);
    }

    $response = array("code" => 100, "success" => true, "m" => sizeof($importErrors), "i" => sizeof($inputErrors), "t" => sizeof($toErrors), "h" => sizeof($hourErrors), "hTemplate" => $hTemplate, "eTemplate" => $eTemplate, "filters" => $filters);
    return new Response(json_encode($response)); 
  }

  public function reviewDetailsAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $userInput = $em
      ->getRepository('SEInputBundle:UserInput')
      ->find($id)
    ;

    if (null === $userInput) {
      throw new NotFoundHttpException("The manhours input ".$id." was not found. Sorry lah.");
    }

    $editorEntry = new EditorEntry();
    $form = $this->createForm(new EditorEntryType($id), $editorEntry, array(
        'action' => $this->generateUrl('se_input_review_edit', array('id' => $id)),
        'method' => 'POST',
    ));

    return $this->render('SEInputBundle:Dashboard:review_details.html.twig', array(
      'input' => $userInput,
      'form' => $form->createView()
    ));
  }

  public function activitiesAction()
  { 
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $id = $request->get('id');
    
    $input = $em
      ->getRepository('SEInputBundle:UserInput')
      ->find($id)
    ;

    $jsonActivities = array(
      'cat' => array(),
      'data' => array()
      );

    if($input){
      foreach ($input->getInputEntries() as $entry) {
        foreach ($entry->getActivityHours() as $a) {
         $key = array_search($a->getActivity()->getName(), $jsonActivities['cat']);
         if($key === false){
            $jsonActivities['cat'][] = $a->getActivity()->getName();
            $key = array_search($a->getActivity()->getName(), $jsonActivities['cat']);
          }
          if(array_key_exists($key, $jsonActivities['data'])){
            $jsonActivities['data'][$key] += $a->getRegularHours() + $a->getOtHours();
          }else{
            $jsonActivities['data'][$key] = $a->getRegularHours() + $a->getOtHours();
          }       
        }
      }
      $response = array("code" => 100, "success" => true, "jsonActivities" => $jsonActivities);

    }else{
      $response = array("code" => 400, "success" => false, "jsonActivities" => $jsonActivities);
    }

    return new Response(json_encode($response)); 
  }

  public function missingInputAction()
  {
    $em = $this->getDoctrine()->getManager();
    $teams = $em->getRepository('SEInputBundle:Team')->getCurrentTeams();
    $userInputs = $em->getRepository('SEInputBundle:UserInput')->getLastMonth();
    $inputIssue = $em->getRepository('SEInputBundle:TypeIssue')->find(2);

    $today = new \DateTime();
    $today->setTime(00, 00, 00);
    $lastMonth = new \DateTime();
    $lastMonth->setTime(00, 00, 00)->modify( '-'.(date('j')-1).' day' );
    $daydiff = $today->diff($lastMonth)->days;
    $found = false;

    for ($i=0; $i < $daydiff; $i++) { //pour chaque jour
        $dateCheck = new \DateTime();
        $dateCheck->setTime(00, 00, 00)->modify( '-'.($i+1).' day' );
      $d = $dateCheck->format("Y-m-d");
      foreach ($teams as $team) { //pour chaque team
        $t = $team->getId();
        for ($s=1; $s <= $team->getShiftnb(); $s++) { //pour chaque shift 
          $shift = $em->getRepository('SEInputBundle:Shift')->findOneBy(array('id' => $s));
          foreach ($userInputs as $u){  // pour chaque userinput 
            if(($u->getDateInput()->format("Y-m-d") == $d) and ($u->getTeam() == $team) and ($u->getShift() == $shift)){$found = true;}
          }
          if(!$found){
            if(!($em->getRepository('SEInputBundle:InputReview')->findOneBy(array('date' => $dateCheck, 'type' => $inputIssue, 'team' => $team, 'shift' =>  $shift)))){
                $m = new InputReview();
                $m->setDate($dateCheck);
                $m->setType($inputIssue);
                $m->setStatus(0);
                $m->setTeam($team);
                $m->setShift($shift);
                $em->persist($m);

                $em->flush();
              }
          }else{
            $found = false;
          }
        }
      }
    }
    $response = array("code" => 100, "success" => true);

    return new Response(json_encode($response)); 
  }

  /**
   * @Security("has_role('ROLE_INPUT_EDIT')")
   */
  public function editPopulateAction()
  {
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $id = $request->get('id');
    $entry = $em->getRepository('SEInputBundle:InputEntry')->getEntryArray($id);
    $request = $em->getRepository('SEInputBundle:EditorEntry')->getEditorEntryId($id);
    
    $response = array("code" => 100, "success" => true, 'entry' => $entry, 'request' => $request);

    return new Response(json_encode($response)); 
  }

  /**
   * @Security("has_role('ROLE_INPUT_EDIT')")
   */
  public function editDeleteAction()
  { 
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $idInput = $request->get('idEntry');
    
    $deleteInput = $em->getRepository('SEInputBundle:InputEntry')->findOneBy(array('id' => $idInput));
    if($deleteInput){
      $deleteActivityHours = $em->getRepository('SEInputBundle:ActivityHours')->findBy(array('input' => $deleteInput));
      if ($deleteActivityHours) {
        foreach ($deleteActivityHours as $deleteActivityHour) {
          // un-record to lines too
          foreach ($em->getRepository('SEInputBundle:SAPRF')->getRecordedTo($deleteInput->getUserInput()->getDateInput(), $deleteInput->getSesa()) as $recordedTo) {
            $recordedTo->setRecorded(0);
          }
          $em->remove($deleteActivityHour);
        }
      }
      $deleteInput->getUserInput()->setProcess(0);
      $em->remove($deleteInput);
      $em->flush();
      $response = array("code" => 100, "success" => true);
    }else{
      $response = array("code" => 400, "success" => false);
    }
    return new Response(json_encode($response)); 
  }

  /**
   * @Security("has_role('ROLE_INPUT_EDIT')")
   */
  public function editorAction(Request $request, $id)
  {
    //This is optional. Do not do this check if you want to call the same action using a regular request.
    if (!$request->isXmlHttpRequest()) {
        return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
    }

    $editorEntry = new EditorEntry();
    $form = $this->createForm(new EditorEntryType($id), $editorEntry, array(
        'action' => $this->generateUrl('se_input_review_edit', array('id' => $id)),
        'method' => 'POST',
    ));
    $form->handleRequest($request);
 
    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($editorEntry);
      
      //add status to associated entry 
      if($editorEntry->getInputEntry()){
        $editorEntry->getInputEntry()->setEditorStatus($editorEntry->getEditorStatus());

        //remove duplicate old version of edition request
        $override = $em->getRepository('SEInputBundle:EditorEntry')->getOverride($editorEntry->getInputEntry()->getId());
        if($override){
          foreach ($override->getEditorActivities() as $a){$em->remove($a);}
          $em->remove($override);
        }
      }

      $em->flush();
 
      return new JsonResponse(array('message' => 'Success!'), 200);
    }
    
    $validator = $this->get('validator');
    $errors = $validator->validate($editorEntry);
    $errorsString = (count($errors) > 0) ? $this->render('SEInputBundle:Utilities:validation.html.twig', array('errors' => $errors)) : "";

    $response = new JsonResponse(
      array(
        'message' => strstr((string) $errorsString, '<h4>'),
        'form' => $this->renderView('SEInputBundle:Dashboard:entry_form.html.twig',
                array(
            'entity' => $editorEntry,
            'form' => $form->createView(),
        ))), 400);
 
    return $response; 
  }

}
