<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\ImageWorker;

class ListController extends Controller
{
    public function indexAction($location, $canonicalUrl, $categoryName)
    {
        // get the list of directories in that location
        $listDir = $this->get('kernel')->getRootDir() . '/../data' . $location;
        $infos = [];

        // each directory is a collection
        foreach(scandir($listDir) as $collection) {
            if($collection == "." || $collection == ".." || !is_dir($listDir.$collection)) {
                continue;
            }
            // get the name of the collection
            $name = preg_replace("/^\d+_(.*)/", "$1", $collection);
            $name = preg_replace("/[_-]+/", " ", $name);
            $miniature = "";
            // now we need a miniature pic for the collection
            // it can be a file with "AP" in the name or a file inside a presentation dir inside the collection
            foreach(scandir($listDir.$collection) as $p) {
                if(
                    preg_match("/AP/",$p) &&
                    preg_match("/image/",mime_content_type($listDir.$collection."/".$p))
                ) {
                    $miniature = $p;
                    break;
                }
                if(is_dir($listDir.$collection."/".$p) && preg_match("/presentation/", $p)) {
                    foreach(scandir($listDir.$collection."/".$p) as $pre) {
                        if(
                            is_file($listDir.$collection."/".$p."/".$pre) && 
                            preg_match("/image/",mime_content_type($listDir.$collection."/".$p."/".$pre))
                        ) {
                            $miniature = $p."/".$pre;
                            break;
                        }
                    }
                }
            }
            // if we have a miniature, then we fill the infos for this collection
            if($miniature != "") {
                $image = "/miniature".$location.$collection."/".$miniature;
                $url = strtolower($location.preg_replace("/^\d+[_-]+/","",$collection));
                $placeholder = "data:image/jpeg;base64,".base64_encode(ImageWorker::getPlaceholder($listDir.$collection."/".$miniature));
                $infos[] = array(
                    "name" => $name,
                    "image" => $image,
                    "url" => $url,
                    "placeholder" => $placeholder
                );
            }
        }
        // get breadcrumbs
        $breadcrumbs = array(
            ["/", "Accueil"],
            [$canonicalUrl, $categoryName]
        );
        return $this->render('@App/list.html.twig', array(
            "list" => $infos,
            "categorie" => $categoryName,
            "title" => $categoryName,
            "breadcrumbs" => $breadcrumbs
        ));
    }
}
