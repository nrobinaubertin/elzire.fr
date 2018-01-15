<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CGVController extends Controller
{
    public function indexAction()
    {
        return $this->render("@App/cgv.html.twig", array(
            "breadcrumbs" => [
                ["/", "Accueil"],
                ["/cgv", "Conditions générales de vente"]
            ],
            "title" => "CONDITION GÉNÉRALES DE VENTE",
            "domain" => $this->getParameter("domain"),
        ));
    }
}
