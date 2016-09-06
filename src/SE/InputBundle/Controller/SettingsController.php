<?php

namespace SE\InputBundle\Controller;

use SE\InputBundle\Entity\Employee;
use SE\InputBundle\Form\EmployeeType;
use SE\InputBundle\Entity\Activity;
use SE\InputBundle\Form\ActivityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class SettingsController extends Controller
{
  	public function employeesAction()
  	{
      $listEmployees = $this->getDoctrine()
        ->getManager()
        ->getRepository('SEInputBundle:Employee')
        ->findByStatusControl(true)
      ;

    	return $this->render('SEInputBundle:Settings:employees.html.twig', array(
        'listEmployees' => $listEmployees
      ));
  	}

  	public function employees_addAction(Request $request)
  	{
      $employee = new Employee();
      $employee->setJobStartDate(new \DateTime());
      $employee->setStartDate(new \DateTime());
      
      $form = $this->createForm(new EmployeeType(), $employee);

      if ($form->handleRequest($request)->isValid()) {
        if ($employee->getSesa != 'NOSESA' && $this->getDoctrine()
                 ->getManager()
                 ->getRepository('SEInputBundle:Employee')
                 ->findOneBy(array('sesa' => $employee->getSesa(), 'statusControl' => 1)))
        {
          $error_message = "This SESA is already used, do you want to be redirected to the employee profile ?";
          return $this->render('SEInputBundle:Settings:employees_add.html.twig', array(
            'form' => $form->createView(),
            'error' => $error_message
            ));
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($employee);
        $em->flush();

      	$request->getSession()->getFlashBag()->add('notice', 'new employee entry saved');

      	return $this->redirect($this->generateUrl('se_input_employees'));
      }
      return $this->render('SEInputBundle:Settings:employees_add.html.twig', array(
      'form' => $form->createView(),
      'error' => $request->get('error'),
      ));
  	}

    public function employees_editAction(Request $request, $id)
    {
      $error_message = $request->query->get('error');
      $repo = $this->getDoctrine()->getManager()->getRepository('SEInputBundle:Employee');
      $employee = $repo->findOneById($id);
      if ($employee->getSesa() != 'NOSESA'){
        $history = $repo->findBy(array('sesa' => $employee->getSesa(), 'statusControl' => 0), array('id' => 'DESC'));
      }
      else {
        $history = null;
      }
      $form = $this->createForm(new EmployeeType(), $employee);

      if ($employee == null && $history == null) {
        $error_message = "This SESA is not used, you have been redirected to the creation page.";
        return $this->redirect($this->generateUrl('se_input_employees_add', array('error' => $error_message)));
      }
      if ($form->handleRequest($request)->isValid()) {
        $employee_draft = clone $employee;
        $employee->setStatusControl(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($employee_draft);
        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'employee entry updated');
      }
      return $this->render('SEInputBundle:Settings:employees_edit.html.twig', array(
      'form' => $form->createView(),
      'history' => $history,
      'error' => $error_message
      ));
    }

  	public function activitiesAction()
  	{
      $listActivities = $this->getDoctrine()
        ->getManager()
        ->getRepository('SEInputBundle:Activity')
        ->findAll()
      ;

    	return $this->render('SEInputBundle:Settings:activities.html.twig', array(
        'listActivities' => $listActivities
      ));
  	}

  	public function activities_addAction(Request $request)
  	{
      $activity = new Activity();
      $form = $this->createForm(new ActivityType(), $activity);

      if ($form->handleRequest($request)->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($activity);
        $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'new activity entry saved');

      return $this->redirect($this->generateUrl('se_input_activities'));
      }

      return $this->render('SEInputBundle:Settings:activities_add.html.twig', array(
      'form' => $form->createView(),
      ));
    }
}
