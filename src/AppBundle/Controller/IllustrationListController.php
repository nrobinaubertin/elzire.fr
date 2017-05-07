<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Utils\ImageWorker;

class IllustrationListController extends Controller
{
    public function indexAction()
    {
        $illustrationDir = $this->get('kernel')->getRootDir() . '/../data/illustrations/';
        $infos = [];

        foreach(scandir($illustrationDir) as $illustration) {
            if($illustration == "." || $illustration == "..") {
                continue;
            }
            $name = preg_replace("/^\d+_(.*)/", "$1", $illustration);
            $name = preg_replace("/[_-]+/", " ", $name);
            $pics = scandir($illustrationDir.$illustration);
            $miniature = "";
            foreach($pics as $p) {
                if(preg_match("/AP/",$p)) {
                    $miniature = $p;
                    break;
                }
            }
            if($miniature != "") {
                $image = "/miniature/illustrations/".$illustration."/".$miniature;
                $url = "/illustrations/".$illustration;
                $placeholder = "data:image/jpeg;base64,".base64_encode(ImageWorker::getPlaceholder($illustrationDir.$illustration."/".$miniature));
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
            ["/illustrations", "Illustrations"]
        );
        return $this->render('AppBundle:Default:illustration-list.html.twig', array(
            "illustrations" => $infos,
            "categorie" => "Illustrations",
            "title" => "Illustrations",
            "breadcrumbs" => $breadcrumbs
        ));
    }
}
