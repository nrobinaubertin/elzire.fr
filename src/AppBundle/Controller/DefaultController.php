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
        return $this->render('@App/landing.html.twig');
    }
    public function elementAction(Request $request)
    {
        return $this->render('@App/element.html.twig');
    }
    public function imageAction($path, Request $request)
    {
        $path = $this->get('kernel')->getRootDir()."/../data/".$path; 
        ImageWorker::displayImage($path, 1200, 1200);
        return new Response("");
    }
    public function miniatureAction($path, Request $request)
    {
        $path = $this->get('kernel')->getRootDir()."/../data/".$path; 
        ImageWorker::displayMiniature($path, 400);
        return new Response("");
    }
}
