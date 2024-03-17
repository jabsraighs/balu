<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Entity\User as EntityUser;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SendEmailService {

    private $mailer;
    private $verifyEmailHelper;

    public function __construct(MailerInterface $mailer,VerifyEmailHelperInterface $verifyEmailHelper) {
    $this->mailer = $mailer;
    $this->verifyEmailHelper = $verifyEmailHelper;

    }

    public function send(string $verifyEmailRouteName,EntityUser $user,String $from, String $to, String $subject, String $template ): void {
         $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $user->getId(),
            $user->getEmail()
        );

        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template);

            $context = $email->getContext();
            $context['signedUrl'] = $signatureComponents->getSignedUrl();
            $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
            $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();
            $email->context($context);
        //envoie du mail
        $this->mailer->send($email);
    }
}