<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PartnersController extends Controller
{
    public function indexAction()
    {
        return $this->render("@App/partners.html.twig", array(
            "breadcrumbs" => [
                ["/", "Accueil"],
                ["/partenaires", "Partenaires"]
            ],
            "title" => "Partenaires",
            "domain" => $this->getParameter("domain"),
        ));
    }
}
