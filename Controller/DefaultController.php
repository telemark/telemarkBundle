<?php

namespace tfk\telemarkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('tfktelemarkBundle:Default:index.html.twig', array('name' => $name));
    }
}
