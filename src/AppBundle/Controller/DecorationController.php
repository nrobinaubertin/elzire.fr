<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DecorationController extends Controller
{
    public function indexAction($category, $dir)
    {
        $familyDir = $this->get('kernel')->getRootDir() . '/../data/decorations/';
        foreach(scandir($familyDir) as $d) {
            if (preg_match("/^\d*[_-]?$category/i", $d)) {
                $category = $d;
                break;
            }
        }
        $tmpDir = "";
        $levenshtein_score = 10000;
        foreach(scandir($familyDir.$category) as $directory) {
            if(
                is_dir($familyDir.$category."/".$directory)
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

        $infos = $this->getFiles($category, $dir);
        return $this->renderHTML($dir, $infos, $category);
    }

    private function getFiles($category, $dir) {
        $decorationDir = $this->get('kernel')->getRootDir()."/../data/decorations/$category/";

        // populate the $files array
        $files = [];
        foreach(scandir($decorationDir.$dir) as $e) {
            if($e == "." || $e == "..") {
                continue;
            }
            $files[] = $e;
        }

        $decorations = [];
        $fullPath = $decorationDir.$dir;
        foreach($files as $file) {
            if(
                $file == "."
                || $file == ".."
                || !is_file($fullPath."/".$file)
            ) {
                continue;
            }

            if ($file == "description.txt") {
                $description = self::convertEncoding(file_get_contents($decorationDir.$dir."/".$file));
                $description = nl2br($description);
                continue;
            }

            if (preg_match("/image/", mime_content_type($fullPath."/".$file))) {
                if(preg_match("/^V/", $file)) {
                    $main_image = "/image/decorations/$category/$dir/" . $file;
                    array_unshift($images, [
                        "/miniature/decorations/$category/$dir/" . $file,
                        "/image/decorations/$category/$dir/" . $file
                    ]);
                } else {
                    $images[] = [
                        "/miniature/decorations/$category/$dir/" . $file,
                        "/image/decorations/$category/$dir/" . $file
                    ];
                }
            }
        }

        if (!isset($description)) {
            $description = "";
        }

        $infos = array(
            "main_image" => $main_image,
            "images" => $images,
            "description" => $description,
        );
        return $infos;
    }

    private function renderHTML($dir, $infos, $category) {
        $main_image = $infos["main_image"];
        $miniatures = $infos["images"];
        $description = $infos["description"];
        $title = preg_replace("/^\d+[_-](.+)/","$1",$dir);
        $title = preg_replace("/[_-]+/"," ",$title);
        $breadcrumbs = array(
            ["/", "Accueil"],
            ["/decorations", "Decorations"],
            ["/decorations/".strtolower($category), ucfirst($category)],
            ["/decorations/".strtolower($category)."/".$dir, $title] 
        );
        
        $others = [];
        $decorationDir = $this->get('kernel')->getRootDir()."/../data/decorations/$category/";
        foreach(scandir($decorationDir) as $i) {
            if($i == "." || $i == ".." || $i == $dir || !is_dir($decorationDir.$i)) {
                continue;
            }
            $other = [];
            $other["url"] = "/decorations/$category/".$i;
            foreach(scandir($decorationDir.$i) as $p) {
                if(preg_match("/AP/",$p) || preg_match("/^V/", $p)) {
                    $other["image"] = "/miniature/decorations/$category/".$i."/".$p;
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
            "categorie" => "Decorations",
            "others" => $others,
            "othersTitle" => "Autres ".strtolower($category)."...",
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
