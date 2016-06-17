<?php

namespace SE\PerformanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SEPerformanceBundle:Performance:index.html.twig', array());
    }
}
