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
        $webDir = $this->get('kernel')->getRootDir() . '/../web';
        $illustrations = scandir($webDir . "/illustration");
        $infos = [];
        foreach($illustrations as $illustration) {
            if($illustration == "." || $illustration == "..") {
                continue;
            }
            $name = preg_replace("/^\d+_(.*)/","$1",$illustration);
            $name = str_replace('-'," ", $name);
            $pics = scandir($webDir . "/illustration/" . $illustration);
            $miniature = "";
            foreach($pics as $p) {
                if(preg_match("/AP/",$p)) {
                    $miniature = $p;
                    break;
                }
            }
            $url = "illustration/" . $illustration . "/" . $miniature;
            $infos[] = array(
                "name" => $name,
                "url" => $url
            );
        }
        return $this->render('AppBundle:Default:illustration-list.html.twig', array(
            "illustrations" => $infos
        ));
    }
}
