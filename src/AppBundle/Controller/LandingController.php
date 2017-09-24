<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LandingController extends Controller
{
    public function indexAction()
    {
        // first we find the root directory of the collection
        $banniereDir = $this->get('kernel')->getRootDir() . '/../data/bannieres/';

        $bannieres = [];
        foreach(scandir($banniereDir) as $dir) {
            if($dir == "." || $dir == ".." || is_file($dir)) {
                continue;
            }
            $url = "";
            $title = "";
            $text = "";
            foreach(scandir($banniereDir.$dir) as $file) {
                if(!is_file($banniereDir.$dir."/".$file)) {
                    continue;
                }
                if(preg_match("/image/", mime_content_type($banniereDir.$dir."/".$file))) {
                    $url = "/banniere/bannieres/".$dir."/".$file;
                } else {
                    if(preg_match("/titre/", $file)) {
                        $title = file_get_contents($banniereDir.$dir."/".$file);
                    } else {
                        $text = file_get_contents($banniereDir.$dir."/".$file);
                    }
                }
            }
            if (!empty($url) && !empty($title) && !empty($text)) {
                $bannieres[] = array(
                    "url" => $url,
                    "title" => $title,
                    "text" => $text
                );
            }
        }

        return $this->render('@App/landing.html.twig', array(
            "bannieres" => $bannieres
        ));

    }
}
