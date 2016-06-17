<?php

namespace SE\InputBundle\Controller;

use SE\InputBundle\Entity\Employee;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Form\UserInputType;
use SE\InputBundle\Entity\InputReview;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SE\InputBundle\SAP;
use SE\InputBundle\Entity\Permission;
use SE\InputBundle\Entity\Abilitation;
use Symfony\Component\Validator\Constraints\Time;

class EntryController extends Controller
{
	public function inputAction(Request $request)
	{

    $listEmployees = $this->getDoctrine()
      ->getManager()
      ->getRepository('SEInputBundle:Employee')
      ->getAlphaCurrentEmployees()
    ;

    $userInput = new UserInput();
    $form = $this->createForm(new UserInputType(), $userInput);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($userInput);
      $em->flush();

    $request->getSession()->getFlashBag()->add('notice', 'new working hours entry saved');

    return $this->redirect($this->generateUrl('se_input_review'));
    }

    return $this->render('SEInputBundle:Entry:input_form.html.twig', array(
    'form' => $form->createView(),
    'listEmployees' => $listEmployees
    ));
	}

	public function menuAction()
  	{
    	return $this->render('SEInputBundle:Entry:menu.html.twig');
  	}

  public function welcomeAction()
    {
    	/*$em = $this->getDoctrine()->getManager();
    	$perm1 = new Permission();
    	$perm1->setName("ROLE_INPUT_READ");
    	$perm1->setDescription("input read");
    	$perm2 = new Permission();
    	$perm2->setName("ROLE_INPUT_COMMENT");
    	$perm2->setDescription("input comment");
    	$abi = new Abilitation();
    	$abi->setName("input commenter");
    	$abi->setDescription("can read and comment inputs");
    	$abi->addPermission($perm1);
    	$abi->addPermission($perm2);
    	$usr = $this->get('security.context')->getToken()->getUser();
    	$usr->addAbilitation($abi);

    	$em->persist($perm1);
    	$em->persist($perm2);
    	$em->persist($abi);
    	$em->persist($usr);
    	$em->flush();*/
    	
      	return $this->render('SEInputBundle:Entry:welcome.html.twig');
    }

  public function populateAction()
  { 
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $idEmployee = $request->get('idEmployee');
    
    $addEmployee = $em->getRepository('SEInputBundle:Employee')->findOneBy(array('id' => $idEmployee, 'statusControl' => 1));
    if($addEmployee){
      $sesa = $addEmployee->getSesa();
      $activity = $addEmployee->getDefaultActivity()->getId();
      $response = array("code" => 100, "success" => true, "sesa" => $sesa, "activity" => $activity);

    }else{
      $response = array("code" => 400, "success" => false);
    }

    return new Response(json_encode($response)); 
  }
}
