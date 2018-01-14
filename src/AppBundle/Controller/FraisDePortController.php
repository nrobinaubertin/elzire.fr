<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FraisDePortController extends Controller
{
    public function indexAction()
    {
        return $this->render("@App/frais-de-port.html.twig", array(
            "breadcrumbs" => [
                ["/", "Accueil"],
                ["/frais-de-port", "Frais de port"]
            ],
            "title" => "FRAIS DE PORT",
            "domain" => $this->getParameter("domain"),
        ));
    }
}
