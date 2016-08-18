<?php

namespace SE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use SE\InputBundle\Entity\Abilitation;

class EmployeesController extends Controller
{
	public function indexAction()
	{
        $em = $this->getDoctrine()->getManager();

		// Load all users from BDD
        $listEmployees = $em->getRepository('SEInputBundle:Employee')->getAll();
        
		return $this->render('SEAdminBundle:Admin:rights.html.twig', array(
            'listEmployees' => $listEmployees,
		));
	}
	
	public function addUserAction(Request $request)
	{
		// Build the form
		$user = new User();
		$form = $this->createForm($this->get('inputBundle_adduser'), $user);
		
		// Handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
		
			// Encode the password
			$password = $this->get('security.password_encoder')
			->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($password);
			$user->setUnreadNotifications(0);
		
			// Save the User
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
		
			// ... do any other work - like sending them an email, etc
			// maybe set a "flash" success message for the user
		
			return $this->redirectToRoute('se_admin_manage_rights');
		}
		
		return $this->render(
				'SEAdminBundle:Admin:addUser.html.twig',
				array('form' => $form->createView())
				);
	}
	
	public function editUserAction($user_id, Request $request)
	{
		// Build the form
		$em = $this->getDoctrine()->getManager();
		$user = $em->find(User::class, $user_id);
    	if (!$user) {
     		throw $this->createNotFoundException( 'No user found for id ' . $user_id );
    	}
		$form = $this->createForm($this->get('inputBundle_edituser'), $user);
		
		// Handle the submit (will only happen on POST)
		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			
			if( $form->isValid()){
				$em->flush();
				return $this->redirectToRoute('se_admin_manage_rights');
			}
		}
		
		return $this->render(
				'SEAdminBundle:Admin:editUser.html.twig',
				array('form' => $form->createView())
				);
	}
	
	public function addAbilitationAction(Request $request)
	{
		// Build the form
		$abilitation = new Abilitation();
		$form = $this->createForm($this->get('inputBundle_addabilitation'), $abilitation);
		
		// Handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
		
			// Save the User
			$em = $this->getDoctrine()->getManager();
			$em->persist($abilitation);
			$em->flush();
		
			// ... do any other work - like sending them an email, etc
			// maybe set a "flash" success message for the user
		
			return $this->redirectToRoute('se_admin_manage_rights');
		}
		
		return $this->render(
				'SEAdminBundle:Admin:addAbilitation.html.twig',
				array('form' => $form->createView())
				);
	}
	
	public function editAbilitationAction($abilitation_id, Request $request)
	{
		// Build the form
		$em = $this->getDoctrine()->getManager();
		$abilitation = $em->find(Abilitation::class, $abilitation_id);
    	if (!$abilitation) {
     		throw $this->createNotFoundException( 'No abilitation found for id ' . $abilitation_id );
    	}
		$form = $this->createForm($this->get('inputBundle_editabilitation'), $abilitation);
		
		// Handle the submit (will only happen on POST)
		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			
			if( $form->isValid()){
				$em->flush();
				return $this->redirectToRoute('se_admin_manage_rights');
			}
		}
		
		return $this->render(
				'SEAdminBundle:Admin:editAbilitation.html.twig',
				array('form' => $form->createView())
				);
	}
	
	public function deleteAbilitationAction($abilitation_id, Request $request)
	{
		// Build the form
		$em = $this->getDoctrine()->getManager();
		$abilitation = $em->find(Abilitation::class, $abilitation_id);
    	if (!$abilitation) {
     		throw $this->createNotFoundException( 'No abilitation found for id ' . $abilitation_id );
    	}
		$form = $this->createFormBuilder($abilitation)->getForm();
		
		// Handle the submit (will only happen on POST)
		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			
			if( $form->isValid()){
      			$em->remove($abilitation);
				$em->flush();
				return $this->redirectToRoute('se_admin_manage_rights');
			}
		}
		
		return $this->render(
				'SEAdminBundle:Admin:deleteAbilitation.html.twig',
				array('form' => $form->createView())
				);
	}
}