<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SurMesureController extends Controller
{
    public function indexAction($dir)
    {
        $surMesureDir = $this->get('kernel')->getRootDir() . '/../data/sur-mesure/';
        $tmpDir = "";
        $levenshtein_score = 10000;
        foreach(scandir($surMesureDir) as $directory) {
            if(
                is_dir($surMesureDir.$directory)
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

        $surMesures = $this->getFiles($dir);

        $main_image = "";
        $description = "";
        $miniatures = [];
        foreach($surMesures as $i) {

            if ($i["file"] == "description.txt") {
                $description = self::convertEncoding(file_get_contents($surMesureDir.$dir."/".$i["file"]));
                $description = nl2br($description);
                continue;
            }

            if($i["id"] == "A" && $i["type"] == "") {
                $main_image = "/image/sur-mesure/" . $dir . "/" . $i["file"];
                continue;
            }

            if($i["type"] != "") {
                $miniatures[] = [
                    "/miniature/sur-mesure/" . $dir . "/" . $i["file"],
                    "/image/sur-mesure/" . $dir . "/" . $i["main"]
                ];
                continue;
            }
        }
        return $this->renderHTML($dir, $main_image, $miniatures, $description);
    }

    private function getFiles($dir) {
        $surMesureDir = $this->get('kernel')->getRootDir() . '/../data/sur-mesure/';

        // populate the $files array
        $files = [];
        foreach(scandir($surMesureDir.$dir) as $e) {
            if($e == "." || $e == "..") {
                continue;
            }
            $files[] = $e;
        }

        $surMesures = [];
        foreach($files as $f) {
            if ($f === "description.txt") {
                $surMesures[] = array(
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
                $surMesures[] = array(
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
        return $surMesures;
    }

    private function renderHTML($dir, $main_image, $miniatures, $description) {
        $title = preg_replace("/^\d+[_-](.+)/","$1",$dir);
        $title = preg_replace("/[_-]+/"," ",$title);
        $breadcrumbs = array(
            ["/", "Accueil"],
            ["/sur-mesure", "Sur mesure"],
            ["/sur-mesure/".$dir, $title] 
        );

        $others = [];
        $surMesureDir = $this->get('kernel')->getRootDir() . '/../data/sur-mesure/';
        foreach(scandir($surMesureDir) as $i) {
            if($i == "." || $i == ".." || $i == $dir) {
                continue;
            }
            $other = [];
            $other["url"] = "/sur-mesure/".$i;
            foreach(scandir($surMesureDir.$i) as $p) {
                if(preg_match("/AP/",$p)) {
                    $other["image"] = "/miniature/sur-mesure/".$i."/".$p;
                    break;
                }
            }
            if(count($other) == 2) {
                $others[] = $other;
            }
        }

        shuffle($others);
        $others = array_slice($others, 0, 5);

        return $this->render('@App/sur-mesure.html.twig',array(
            "main_image" => $main_image,
            "miniatures" => $miniatures,
            "breadcrumbs" => $breadcrumbs,
            "title" => $title,
            "categorie" => "Sur-Mesure",
            "others" => $others,
            "othersTitle" => "Autres sur mesure...",
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
