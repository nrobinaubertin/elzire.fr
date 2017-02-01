<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('AppBundle:Default:landing.html.twig');
    }
    public function elementAction(Request $request)
    {
        return $this->render('AppBundle:Default:element.html.twig');
    }
    public function illustrationAction(Request $request)
    {
        return $this->render('AppBundle:Default:illustration.html.twig');
    }
    public function illustrationListAction(Request $request)
    {
        return $this->render('AppBundle:Default:illustration-list.html.twig');
    }
}
