<?php
/**
 * Created by IntelliJ IDEA.
 * User: louis aumaitre
 * Date: 24/05/2016
 * Time: 10:46
 */

namespace SE\InputBundle\Controller;

use SE\InputBundle\Form\UserType;
use SE\InputBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="user_login")
     */
    public function loginAction(Request $request)
    {
        /*$session = $request->getSession();

        // \Symfony\Component\Security\Core\Security
        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }
        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);
        // security.csrf.token_manager
        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        
        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error' => $error,
            //'csrf_token' => $csrfToken,
        ));*/

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('/');
        }
        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('OCUserBundle:Security:login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
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
}