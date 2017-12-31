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
        $email_sent = !is_null($request->query->get("sent"));
        $success = $request->query->get("sent");
        return $this->render("@App/contact.html.twig", array(
            "breadcrumbs" => [
                ["/", "Accueil"],
                ["/contact", "contact"]
            ],
            "title" => "CONTACT",
            "email_sent" => $email_sent,
            "success" => $success,
            "g_recaptcha_key" => $this->getParameter("google_recaptcha_site_key")
        ));
    }

    public function sendAction(Request $request)
    {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $data = [
            "secret" => $this->getParameter("google_recaptcha_secret_key"),
            "response" => $request->request->get("g-recaptcha-response"),

        ];
        $options = array(
            "http" => array(
                "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
                "method"  => "POST",
                "content" => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if (!empty($result)) {
            $result = json_decode($result, true);
            if ($result["success"]) {
                if (empty($request->request->get("subject"))) {
                    $subject = "Message de ".$request->request->get("sender"); 
                } else {
                    $subject = $request->request->get("subject");
                }
                $message = (new \Swift_Message($subject))
                    ->setFrom([$request->request->get("sender") => "Page contact"])
                    ->setTo($this->getParameter("mail_to"))
                    ->setBody($request->request->get("message"), "text/plain")
                    ->setReplyTo([$request->request->get("sender")]);

                $this->get("mailer")->send($message);
                return $this->redirect("/contact?sent=1", Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirect("/contact?sent=0", Response::HTTP_SEE_OTHER);
            }
        }
    }
}
