<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IllustrationListController extends Controller
{
    public function indexAction()
    {
        $webDir = $this->get('kernel')->getRootDir() . '/../web';
        $illustrations = scandir($webDir . "/illustrations");
        $infos = [];
        foreach($illustrations as $illustration) {
            if($illustration == "." || $illustration == "..") {
                continue;
            }
            $name = preg_replace("/^\d+_(.*)/", "$1", $illustration);
            $name = preg_replace("/[_-]+/", " ", $name);
            $pics = scandir($webDir . "/illustrations/" . $illustration);
            $miniature = "";
            foreach($pics as $p) {
                if(preg_match("/AP/",$p)) {
                    $miniature = $p;
                    break;
                }
            }
            if($miniature != "") {
                $image = "illustrations/" . $illustration . "/" . $miniature;
                $url = "illustrations/" . $illustration;
                $infos[] = array(
                    "name" => $name,
                    "image" => $image,
                    "url" => $url
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
