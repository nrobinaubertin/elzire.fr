<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CollectionController extends Controller
{
    public function indexAction($dir)
    {
        // first we find the root directory of the collection
        $collectionDir = $this->get('kernel')->getRootDir() . '/../data/mariages/';
        foreach(scandir($collectionDir) as $directory) {
            if(
                is_dir($collectionDir.$directory)
                && preg_match("/".$dir."/i", $directory)
            ) {
                $dir = $directory;
                break;
            }
        }

        $infos = $this->getFiles($collectionDir.$dir);

        $main_image = "/image/mariages/" . $dir . "/" . $infos["main_image"];
        foreach($infos["elements"] as $k=>$v) {
            $infos["elements"][$k]["miniature"] = "/miniature/mariages/" . $dir . "/" . $v["miniature"];
        }
        return $this->renderHTML($dir, $main_image, $infos["elements"]);
    }

    public function getFiles($dir)
    {
        // now we need to find all the elements of the collection
        $elements = [];
        $main_image = "";
        foreach(scandir($dir) as $elementDir) {
            if($elementDir == "." || $elementDir == ".." || is_file($elementDir)) {
                continue;
            }
            // the "presentation" dir is here only for the main_image
            if(preg_match("/^\d+[_-]presentation/",$elementDir)) {
                foreach(scandir($dir."/".$elementDir) as $file) {
                    if(
                        is_file($dir."/".$elementDir."/".$file)
                        && preg_match("/image/", mime_content_type($dir."/".$elementDir."/".$file))
                    ) {
                        $main_image = $elementDir."/".$file;
                        break;
                    }
                }
                continue;
            }
            $element = array(
                "name" => $this->getName($elementDir),
                "url" => $this->getNiceUrl($elementDir)
            );
            // let's find the miniature for this element
            foreach(scandir($dir."/".$elementDir) as $file) {
                if(
                    is_file($dir."/".$elementDir."/".$file)
                    && preg_match("/image/", mime_content_type($dir."/".$elementDir."/".$file))
                    && preg_match("/^V/", $file)
                ) {
                    $element["miniature"] = $elementDir."/".$file;
                    break;
                }
            }
            $elements[] = $element;
        }

        $infos = array(
            "main_image" => $main_image,
            "elements" => $elements
        );
        return $infos;
    }

    private function getNiceUrl($url) {
        return strtolower(preg_replace("/^\d+[_-]+/","", $url));
    }

    private function getName($str) {
        $name = preg_replace("/^\d+[_-](.+)/","$1",$str);
        $name = preg_replace("/[_-]+/"," ",$name);
        return $name;
    }

    private function renderHTML($dir, $main_image, $elements) {
        $title = $this->getName($dir);
        $breadcrumbs = array(
            ["/", "Accueil"],
            ["/mariages", "Mariages"],
            ["/mariages/".$dir, $title] 
        );
        
        $others = [];
        $collectionDir = $this->get('kernel')->getRootDir() . '/../data/mariages/';
        foreach(scandir($collectionDir) as $e) {
            if($e == "." || $e == ".." || !is_dir($collectionDir.$e)) {
                continue;
            }
            $others[] = array(
                "url" => "/mariages/".$this->getNiceUrl($e),
                "name" => $this->getName($e)
            );
        }

        return $this->render('@App/collection.html.twig',array(
            "main_image" => $main_image,
            "elements" => $elements,
            "breadcrumbs" => $breadcrumbs,
            "categorie" => "Mariages - ".$title,
            "others" => $others,
            "title" => "",
            "subtitle" => "",
        ));
    }
}
