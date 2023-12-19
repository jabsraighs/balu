<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


class MailSendService {

    private $mailer;

    public function __construct(MailerInterface $mailer) {
    $this->mailer = $mailer;


    }

    public function send(String $from, String $to, String $subject, String $template,array $context): void {

        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        //envoie du mail
        $this->mailer->send($email);
    }
}