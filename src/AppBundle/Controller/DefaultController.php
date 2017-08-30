<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\ImageWorker;

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
    public function imageAction($path, Request $request)
    {
        $path = $this->get('kernel')->getRootDir()."/../data/".$path; 
        ImageWorker::displayImage($path, 1024, 1024);
        return new Response("");
    }
    public function miniatureAction($path, Request $request)
    {
        $path = $this->get('kernel')->getRootDir()."/../data/".$path; 
        ImageWorker::displayMiniature($path);
        return new Response("");
    }
}
