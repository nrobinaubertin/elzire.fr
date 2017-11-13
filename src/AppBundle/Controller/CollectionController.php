<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CollectionController extends Controller
{
    public function indexAction($collection)
    {
        // first we find the root directory of the collection
        $listDir = $this->get('kernel')->getRootDir() . '/../data/mariages/';
        foreach(scandir($listDir) as $directory) {
            if(
                is_dir($listDir.$directory)
                && preg_match("/".$collection."/i", $directory)
            ) {
                $collectionDir = $directory;
                break;
            }
        }

        $infos = $this->getFiles($listDir, $collectionDir);

        $infos["main_image"] = "/image/mariages/" . $collectionDir . "/" . $infos["main_image"];
        foreach($infos["elements"] as $k=>$v) {
            $infos["elements"][$k]["miniature"] = "/miniature/mariages/" . $collectionDir . "/" . $v["miniature"];
        }
        return $this->renderHTML($collectionDir, $infos);
    }

    public function getFiles($listDir, $collectionDir)
    {
        // now we need to find all the elements of the collection
        $elements = [];
        $main_image = "";
        $title = "";
        foreach(scandir($listDir.$collectionDir) as $elementDir) {
            if($elementDir == "." || $elementDir == ".." || is_file($elementDir)) {
                continue;
            }
            // the "presentation" dir is here only for the main_image
            if(preg_match("/^\d+[_-]presentation/",$elementDir)) {
                foreach(scandir($listDir.$collectionDir."/".$elementDir) as $file) {
                    if(preg_match("/texte\-presentation/", $file)) {
                        $title = nl2br(file_get_contents($listDir.$collectionDir."/".$elementDir."/".$file));
                        continue;
                    }
                    if(
                        is_file($listDir.$collectionDir."/".$elementDir."/".$file)
                        && preg_match("/image/", mime_content_type($listDir.$collectionDir."/".$elementDir."/".$file))
                    ) {
                        $main_image = $elementDir."/".$file;
                        continue;
                    }
                }
                continue;
            }
            $element = array(
                "name" => $this->getName($elementDir),
                "url" => $this->getNiceUrl($collectionDir."/".$elementDir)
            );
            // let's find the miniature for this element
            foreach(scandir($listDir.$collectionDir."/".$elementDir) as $file) {
                if(
                    is_file($listDir.$collectionDir."/".$elementDir."/".$file)
                    && preg_match("/image/", mime_content_type($listDir.$collectionDir."/".$elementDir."/".$file))
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
            "elements" => $elements,
            "title" => $title
        );
        return $infos;
    }

    private function getNiceUrl($url) {
        return strtolower(preg_replace("/\d+[_-]+/","", $url));
    }

    private function getName($str) {
        $name = preg_replace("/^\d+[_-](.+)/","$1",$str);
        $name = preg_replace("/[_-]+/"," ",$name);
        return ucfirst($name);
    }

    private function renderHTML($collectionDir, $infos) {
        $collection = $this->getName($collectionDir);
        $breadcrumbs = array(
            ["/", "Accueil"],
            ["/mariages", "Mariages"],
            ["/mariages/".$collectionDir, $collection] 
        );
        
        $others = [];
        $listDir = $this->get('kernel')->getRootDir() . '/../data/mariages/';
        foreach(scandir($listDir) as $e) {
            if($e == "." || $e == ".." || !is_dir($listDir.$e)) {
                continue;
            }
            $others[] = array(
                "url" => "/mariages/".$this->getNiceUrl($e),
                "name" => $this->getName($e)
            );
        }

        return $this->render('@App/collection.html.twig',array(
            "main_image" => $infos["main_image"],
            "elements" => $infos["elements"],
            "collection_title" => $infos["title"],
            "breadcrumbs" => $breadcrumbs,
            "categorie" => "COLLECTIONS MARIAGE",
            "others" => $others,
            "title" => "",
            "subtitle" => "",
        ));
    }
}
