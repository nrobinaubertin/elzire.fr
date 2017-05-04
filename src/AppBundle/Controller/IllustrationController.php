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
        foreach(scandir($webDir . "/illustrations/" . $dir) as $e) {
            if($e == "." || $e == "..") {
                continue;
            }
            $files[] = $e;
        }

        $illustrations = [];
        foreach($files as $f) {
            $infos = preg_replace("/^(\d+)[_-]([A-Z])([A-Z])?[_-]([a-z\-]+).*/","$1 $2 $3 $4",$f);
            $infos = explode(" ", $infos);
            // look for main file
            foreach($files as $main) {
                if(preg_match("/^".$infos[0]."[_-]".substr($infos[1], 0, 1)."[_-]".$infos[3].".*/",$main)) {
                    $infos[] = $main;
                    break;
                }
            }
            if(count($infos) > 3) {
                $illustrations[] = array(
                    "file" => $f,
                    "num" => $infos[0],
                    "id" => $infos[1],
                    "type" => $infos[2],
                    "name" => $infos[3],
                    "main" => $infos[4]
                );
            }
        }
        return $illustrations;
    }

    private function renderHTML($dir, $main_image, $miniatures) {
        $title = preg_replace("/^\d+[_-](.+)/","$1",$dir);
        $title = preg_replace("/[_-]+/"," ",$title);
        $breadcrumbs = array(
            ["/", "Accueil"],
            ["/illustrations", "Illustrations"],
            ["/illustrations/".$dir, $title] 
        );
        
        $others = [];
        $webDir = $this->get('kernel')->getRootDir() . '/../web';
        foreach(scandir($webDir . "/illustrations") as $i) {
            if($i == "." || $i == ".." || $i == $dir) {
                continue;
            }
            $other = [];
            $other["url"] = "/illustrations/".$i;
            foreach(scandir($webDir."/illustrations/".$i) as $p) {
                if(preg_match("/AP/",$p)) {
                    $other["image"] = "/illustrations/".$i."/".$p;
                    break;
                }
            }
            if(count($other) == 2) {
                $others[] = $other;
            }
        }

        shuffle($others);
        $others = array_slice($others, 0, 3);

        return $this->render('AppBundle:Default:illustration.html.twig',array(
            "main_image" => $main_image,
            "miniatures" => $miniatures,
            "breadcrumbs" => $breadcrumbs,
            "title" => $title,
            "categorie" => "Illustrations",
            "others" => $others,
            "othersTitle" => "Autres illustrations..."
        ));
    }

    public function indexAction($dir)
    {
        $illustrations = $this->getFiles($dir);

        $main_image = "";
        $miniatures = [];
        foreach($illustrations as $i) {
            if($i["id"] == "A" && $i["type"] == "") {
                $main_image = "/illustrations/" . $dir . "/" . $i["file"];
                continue;
            }
            if($i["type"] != "") {
                $miniatures[] = array(
                    "/illustrations/" . $dir . "/" . $i["file"],
                    "/illustrations/" . $dir . "/" . $i["main"]
                );
                continue;
            }
        }
        return $this->renderHTML($dir, $main_image, $miniatures);
    }
}
