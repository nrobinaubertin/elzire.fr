<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IllustrationController extends Controller
{

    private function getFiles($dir) {
        $webDir = $this->get('kernel')->getRootDir() . '/../web';

        // populate the $files array
        $files = [];
        foreach(scandir($webDir . "/illustration/" . $dir) as $e) {
            if($e == "." || $e == "..") {
                continue;
            }
            $files[] = $e;
        }

        $illustrations = [];
        foreach($files as $f) {
            $infos = preg_replace("/^(\d+)[_-]([A-Z])([A-Z])?[_-]([a-z\-]+).*/","$1 $2 $3 $4",$f);
            $infos = explode(" ", $infos);
            if(count($infos) > 3) {
                $illustrations[] = array(
                    "file" => $f,
                    "num" => $infos[0],
                    "id" => $infos[1],
                    "type" => $infos[2],
                    "name" => $infos[3]
                );
            }
        }
        return $illustrations;
    }

    public function indexAction($dir)
    {
        $illustrations = $this->getFiles($dir);

        $main_image = "";
        $miniatures = [];
        foreach($illustrations as $i) {
            if($i["id"] == "A" && $i["type"] == "") {
                $main_image = "/illustration/" . $dir . "/" . $i["file"];
                continue;
            }
            if($i["type"] != "") {
                $miniatures[] = array(
                    "/illustration/" . $dir . "/" . $i["file"],
                    $i["id"]
                );
                continue;
            }
        }

        return $this->render('AppBundle:Default:illustration.html.twig',array(
            "main_image" => $main_image,
            "miniatures" => $miniatures
        ));
    }
    public function illustrationAction($dir, $id)
    {
        $illustrations = $this->getFiles($dir);
        
        $main_image = "";
        $miniatures = [];
        foreach($illustrations as $i) {
            if($i["id"] == $id && $i["type"] == "") {
                $main_image = "/illustration/" . $dir . "/" . $i["file"];
                continue;
            }
            if($i["type"] != "") {
                $miniatures[] = array(
                    "/illustration/" . $dir . "/" . $i["file"],
                    $i["id"]
                );
                continue;
            }
        }

        return $this->render('AppBundle:Default:illustration.html.twig',array(
            "main_image" => $main_image,
            "miniatures" => $miniatures
        ));
    }
}
