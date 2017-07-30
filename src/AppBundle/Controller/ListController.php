<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Utils\ImageWorker;

class ListController extends Controller
{
    public function indexAction($location, $canonicalUrl, $categoryName)
    {
        $location = '/illustrations/';
        $canonicalUrl = '/illustrations';
        $categoryName = 'Illustrations';

        $listDir = $this->get('kernel')->getRootDir() . '/../data' . $location;
        $infos = [];

        foreach(scandir($listDir) as $collection) {
            if($collection == "." || $collection == "..") {
                continue;
            }
            $name = preg_replace("/^\d+_(.*)/", "$1", $collection);
            $name = preg_replace("/[_-]+/", " ", $name);
            $pics = scandir($listDir.$collection);
            $miniature = "";
            foreach($pics as $p) {
                if(preg_match("/AP/",$p)) {
                    $miniature = $p;
                    break;
                }
            }
            if($miniature != "") {
                $image = "/miniature".$location.$collection."/".$miniature;
                $url = $location.$collection;
                $placeholder = "data:image/jpeg;base64,".base64_encode(ImageWorker::getPlaceholder($listDir.$collection."/".$miniature));
                $infos[] = array(
                    "name" => $name,
                    "image" => $image,
                    "url" => $url,
                    "placeholder" => $placeholder
                );
            }
        }

        $breadcrumbs = array(
            ["/", "Accueil"],
            [$canonicalUrl, $categoryName]
        );
        return $this->render('AppBundle:Default:list.html.twig', array(
            "list" => $infos,
            "categorie" => $categoryName,
            "title" => $categoryName,
            "breadcrumbs" => $breadcrumbs
        ));
    }
}
