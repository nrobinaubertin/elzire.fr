<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IllustrationController extends Controller
{
    public function indexAction($dir)
    {
        $illustrationDir = $this->get('kernel')->getRootDir() . '/../data/illustrations/';
        $tmpDir = "";
        $levenshtein_score = 10000;
        foreach(scandir($illustrationDir) as $directory) {
            if(
                is_dir($illustrationDir.$directory)
                && stristr($directory, $dir) !== false
            ) {
                $l = levenshtein($dir, $directory);
                if ($l < $levenshtein_score) {
                    $tmpDir = $directory;
                    $levenshtein_score = $l;
                }
            }
        }
        $dir = $tmpDir;

        $illustrations = $this->getFiles($dir);

        $main_image = "";
        $description = "";
        $miniatures = [];
        foreach($illustrations as $i) {

            if ($i["file"] == "description.txt") {
                $description = self::convertEncoding(file_get_contents($illustrationDir.$dir."/".$i["file"]));
                $description = nl2br($description);
                continue;
            }

            if($i["id"] == "A" && $i["type"] == "") {
                $main_image = "/image/illustrations/" . $dir . "/" . $i["file"];
                continue;
            }

            if($i["type"] != "") {
                $miniatures[] = [
                    "/miniature/illustrations/" . $dir . "/" . $i["file"],
                    "/image/illustrations/" . $dir . "/" . $i["main"]
                ];
                continue;
            }
        }
        return $this->renderHTML($dir, $main_image, $miniatures, $description);
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
            if ($f === "description.txt") {
                $illustrations[] = array(
                    "file" => $f,
                    "ext" => pathinfo($f, PATHINFO_EXTENSION)
                );
                continue;
            }
            $infos = preg_replace("/^(\d+)[_-]([A-Z])([A-Z])?[_-]([A-Za-z\-]+).*/","$1 $2 $3 $4",$f);
            $infos = explode(" ", $infos);
            if (count($infos) < 4) {
                continue;
            }
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
                    "main" => $infos[4],
                    "ext" => pathinfo($f, PATHINFO_EXTENSION)
                );
            }
        }
        return $illustrations;
    }

    private function renderHTML($dir, $main_image, $miniatures, $description) {
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
            "othersTitle" => "Autres illustrations...",
            "description" => $description,
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
