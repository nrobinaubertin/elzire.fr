<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function indexAction()
    {
        return $this->render('@App/contact.html.twig', array(
            "breadcrumbs" => [
                ["/", "Accueil"],
                ["/contact", "contact"]
            ],
            "title" => "CONTACT",
        ));
    }
}
