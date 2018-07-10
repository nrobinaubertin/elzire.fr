<?php

namespace AppBundle\Controller;

use AppBundle\Utils\ImageWorker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function elementAction(Request $request)
    {
        return $this->render('@App/element.html.twig');
    }
    public function imageAction($path, Request $request)
    {
        $path = $this->get('kernel')->getRootDir()."/../data/".$path;
        $rootDir = realpath($this->get('kernel')->getRootDir()."/..");
        $watermark = $this->get('kernel')->getRootDir()."/../web/assets/watermark.png";

        $imageWorker = new ImageWorker($rootDir."/var/cache/thumbs");
        $imageWorker->displayImage($path, 1200, 1200, $watermark);
        return new Response("");
    }
    public function assetsAction($path, Request $request)
    {
        $path = $this->get('kernel')->getRootDir()."/../web/assets/".$path;
        $rootDir = realpath($this->get('kernel')->getRootDir()."/..");
        $imageWorker = new ImageWorker($rootDir."/var/cache/thumbs");
        $imageWorker->displayImage($path, 640, 640, null);
        return new Response("");
    }
    public function banniereAction($path, Request $request)
    {
        $path = $this->get('kernel')->getRootDir()."/../data/".$path;
        $rootDir = realpath($this->get('kernel')->getRootDir()."/..");
        $watermark = "";

        $imageWorker = new ImageWorker($rootDir."/var/cache/thumbs");
        $imageWorker->displayImage($path, 1200, 1200, $watermark);
        return new Response("");
    }
    public function miniatureAction($path, Request $request)
    {
        $path = $this->get('kernel')->getRootDir()."/../data/".$path;
        $rootDir = realpath($this->get('kernel')->getRootDir()."/..");
        $imageWorker = new ImageWorker($rootDir."/var/cache/thumbs");
        $imageWorker->displayMiniature($path, 400);
        return new Response("");
    }
}
