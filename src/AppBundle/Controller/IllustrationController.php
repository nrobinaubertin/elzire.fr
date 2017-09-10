<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IllustrationController extends Controller
{
    public function indexAction($dir)
    {
        $illustrationDir = $this->get('kernel')->getRootDir() . '/../data/illustrations/';
        foreach(scandir($illustrationDir) as $directory) {
            if(
                is_dir($illustrationDir.$directory)
                && preg_match("/".$dir."/i", $directory)
            ) {
                $dir = $directory;
                break;
            }
        }

        $illustrations = $this->getFiles($dir);

        $main_image = "";
        $miniatures = [];
        foreach($illustrations as $i) {
            if($i["id"] == "A" && $i["type"] == "") {
                $main_image = "/image/illustrations/" . $dir . "/" . $i["file"];
                continue;
            }
            if($i["type"] != "") {
                $miniatures[] = array(
                    "/miniature/illustrations/" . $dir . "/" . $i["file"],
                    "/image/illustrations/" . $dir . "/" . $i["main"]
                );
                continue;
            }
        }
        return $this->renderHTML($dir, $main_image, $miniatures);
    }

    private function getFiles($dir) {
        $illustrationDir = $this->get('kernel')->getRootDir() . '/../data/illustrations/';

        // populate the $files array
        $files = [];
        foreach(scandir($illustrationDir.$dir) as $e) {
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
        $illustrationDir = $this->get('kernel')->getRootDir() . '/../data/illustrations/';
        foreach(scandir($illustrationDir) as $i) {
            if($i == "." || $i == ".." || $i == $dir) {
                continue;
            }
            $other = [];
            $other["url"] = "/illustrations/".$i;
            foreach(scandir($illustrationDir.$i) as $p) {
                if(preg_match("/AP/",$p)) {
                    $other["image"] = "/miniature/illustrations/".$i."/".$p;
                    break;
                }
            }
            if(count($other) == 2) {
                $others[] = $other;
            }
        }

        shuffle($others);
        $others = array_slice($others, 0, 5);

        return $this->render('@App/illustration.html.twig',array(
            "main_image" => $main_image,
            "miniatures" => $miniatures,
            "breadcrumbs" => $breadcrumbs,
            "title" => $title,
            "categorie" => "Illustrations",
            "others" => $others,
            "othersTitle" => "Autres illustrations..."
        ));
    }
}
