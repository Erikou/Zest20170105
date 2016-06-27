<?php

namespace SE\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NotificationController extends Controller
{
    public function indexAction()
    {
    	$listNotifications = $this->getDoctrine()
      		->getManager()
      		->getRepository('SEInputBundle:Notification')
      		->getByUser($this->getUser()->getId())
    	;
    	
        return $this->render('SENotificationBundle::global.html.twig', array(
        		'notifs' => $listNotifications
        ));
    }

    public function detailedAction($id)
    {
    	$listNotifications = $this->getDoctrine()
    	->getManager()
    	->getRepository('SEInputBundle:Notification')
    	->getByUser($this->getUser()->getId())
    	;
    	
    	$detailed = null;
    	foreach ($listNotifications as $notif){
    		if ($notif->getId() == $id){
    			$detailed = $notif;
    			$notif->setHasBeenRead(true);
				$em = $this->getDoctrine()->getManager();
				$em->persist($notif);
				$em->flush();
    		}
    	}
    	 
    	return $this->render('SENotificationBundle::detailed.html.twig', array(
    			'notifs' => $listNotifications,
    			'det_notif' => $detailed
    	));
    }
}
