<?php

namespace SE\TransferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SETransferBundle:Default:index.html.twig', array('name' => $name));
    }
}
