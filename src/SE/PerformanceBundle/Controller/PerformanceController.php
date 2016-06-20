<?php

namespace SE\PerformanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PerformanceController extends Controller
{
    public function indexAction()
    {
        return $this->render('SEPerformanceBundle:Performance:index.html.twig', array());
    }

    public function menuAction()
	{
		return $this->render('SEPerformanceBundle:Performance:menu.html.twig');
	}
}
