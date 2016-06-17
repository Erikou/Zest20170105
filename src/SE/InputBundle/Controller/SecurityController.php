<?php
/**
 * Created by IntelliJ IDEA.
 * User: louis aumaitre
 * Date: 24/05/2016
 * Time: 10:46
 */

namespace SE\InputBundle\Controller;

use SE\InputBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormError;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->renderLogin(array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('Security/login.html.twig', $data);
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
    
    public function accountAction(Request $request)
    {
		// Build the form
		$em = $this->getDoctrine()->getManager();
		$user =  $this->get('security.context')->getToken()->getUser();
		$form1 = $this->get('form.factory')->createNamedBuilder('edit-account',
				$this->get('inputBundle_editaccount'), $user)->getForm();
		$form2 = $this->get('form.factory')->createNamedBuilder('edit-password',
				$this->get('inputBundle_editpassword'))->getForm();
		
		// Handle the submit (will only happen on POST)
		if ($request->getMethod() == 'POST') {
    		if ($request->request->has($form1->getName())
    				&& $this->get('security.context')->isGranted('ROLE_CAN_CHANGE_CREDENTIALS')) {
				$form1->handleRequest($request);
				if( $form1->isValid()){
					$em->flush();
					return $this->redirectToRoute('account');
				}
    		} else if ($request->request->has($form2->getName())) {
				$form2->handleRequest($request);
				if( $form2->isValid()){
					$user->setPlainPassword($form2['newPassword']->getData());
					$password = $this->get('security.password_encoder')
						->encodePassword($user, $user->getPlainPassword());
					$user->setPassword($password);
					$em->flush();
					return $this->redirectToRoute('account');
				}
    		}
		}
		
        return $this->render('Security/account.html.twig',
        		array('form1' => $form1->createView(), 'form2' => $form2->createView(), 'user' => $user));
    	
    }
}