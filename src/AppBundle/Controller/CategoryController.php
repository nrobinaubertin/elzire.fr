<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\ImageWorker;

class CategoryController extends Controller
{
    public function indexAction($family)
    {
        $rootDir = realpath($this->get('kernel')->getRootDir()."/..");
        $imageWorker = new ImageWorker($rootDir."/data/cache/thumbs");
        // get the list of directories in that location
        $listDir = $this->get('kernel')->getRootDir() . "/../data/$family/";
        $infos = [];

        // each directory is a category
        foreach(scandir($listDir) as $category) {
            if($category == "." || $category == ".." || !is_dir($listDir.$category)) {
                continue;
            }

            // get the name of the collection
            $name = preg_replace("/^\d+_(.*)/", "$1", $category);
            $name = preg_replace("/[_-]+/", " ", $name);
            $miniature = "";

            foreach(scandir($listDir.$category) as $p) {
                if(
                    preg_match("/image/",mime_content_type($listDir.$category."/".$p))
                ) {
                    $miniature = $p;
                    break;
                }
            }

            // if we have a miniature, then we fill the infos for this category
            if($miniature != "") {
                $image = "/miniature/$family/".$category."/".$miniature;
                $url = strtolower("/$family/".preg_replace("/^\d+[_-]+/","",$category));
                $infos[] = array(
                    "name" => $name,
                    "image" => $image,
                    "url" => $url,
                    "activated" => count(scandir($listDir.$category)) > 3
                );
            }
        }

        // get breadcrumbs
        $breadcrumbs = array(
            ["/", "Accueil"],
            ["/$family", ucfirst($family)],
        );
        return $this->render('@App/category.html.twig', array(
            "list" => $infos,
            "title" => ucfirst($family),
            "breadcrumbs" => $breadcrumbs,
            "domain" => $this->getParameter("domain"),
        ));
    }
}
