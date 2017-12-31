<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function indexAction(Request $request)
    {
        $email_sent = !empty($request->query->get('sent'));
        return $this->render('@App/contact.html.twig', array(
            "breadcrumbs" => [
                ["/", "Accueil"],
                ["/contact", "contact"]
            ],
            "title" => "CONTACT",
            "email_sent" => $email_sent
        ));
    }

    public function sendAction(Request $request)
    {
        $message = (new \Swift_Message('Mail de contact'))
            ->setFrom($request->request->get('sender'))
            ->setTo($this->getParameter('mail_to'))
            ->setBody($request->request->get('message'), 'text/plain');

        $this->get('mailer')->send($message);
        return $this->redirect("/contact?sent=1", Response::HTTP_SEE_OTHER);
    }
}
