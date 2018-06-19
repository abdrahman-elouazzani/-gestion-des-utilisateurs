<?php

namespace MembreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MembreBundle:Default:index.html.twig');
    }
}
