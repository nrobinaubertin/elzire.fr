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
            $image = "";
            $title = "";
            $text = "";
            $url = "";
            foreach(scandir($banniereDir.$dir) as $file) {
                if(!is_file($banniereDir.$dir."/".$file)) {
                    continue;
                }
                if(preg_match("/image/", mime_content_type($banniereDir.$dir."/".$file))) {
                    $image = "/banniere/bannieres/".$dir."/".$file;
                } else {
                    if(preg_match("/titre/", $file)) {
                        $title = self::convertEncoding(file_get_contents($banniereDir.$dir."/".$file));
                        continue;
                    }
                    if(preg_match("/texte/", $file)) {
                        $text = self::convertEncoding(file_get_contents($banniereDir.$dir."/".$file));
                        continue;
                    }
                    if(preg_match("/lien/", $file)) {
                        $url = file_get_contents($banniereDir.$dir."/".$file);
                        continue;
                    }
                }
            }
            if (!empty($image) && !empty($title) && !empty($text)) {
                $bannieres[] = array(
                    "image" => $image,
                    "title" => $title,
                    "text" => $text,
                    "url" => $url
                );
            }
        }

        return $this->render('@App/landing.html.twig', array(
            "bannieres" => $bannieres,
            "domain" => $this->getParameter("domain"),
        ));

    }

    private function convertEncoding($str) {
        $enc = mb_detect_encoding($str, mb_detect_order(), true);
        if ($enc === "ISO-8859-1") {
            return utf8_encode($str);
        }
        if($enc === "Windows-1252" || !$enc) {
            return mb_convert_encoding($str, "UTF-8", "Windows-1252");
        }
        return $str;
    }
}
