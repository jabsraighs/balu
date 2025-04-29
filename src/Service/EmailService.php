<?php

namespace App\Service;

use App\Entity\Invoice;
use App\Entity\Quote;
use Dompdf\Dompdf;
use App\Entity\Invitation;
use Dompdf\Options;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EmailService
{
    private $mailer;
    private $pdf;
    private $twig;
    private $params;
    private $router;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        ParameterBagInterface $params,
        UrlGeneratorInterface $router
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->params = $params;
        $this->router = $router;
    }

    /**
     * Envoie un email avec un devis en pièce jointe
     */
    public function sendQuoteEmail(Quote $quote, array $customVars = []): bool
    {
        $pdfHtml = $this->twig->render('quote/pdf.html.twig', [
            'quote' => $quote
        ]);

        // $pdfContent = $this->pdf->getOutputFromHtml($pdfHtml);

        // Variables par défaut
        $variables = [
            'quote' => $quote,
            'app_url' => $this->params->get('app_url'),
            'company_name' => $quote->getCompany()->getName(),
        ];

        // Ajouter les variables personnalisées
        $variables = array_merge($variables, $customVars);

        $emailHtml = $this->twig->render('quote/email.html.twig', $variables);

        $email = (new Email())
            ->from($this->params->get('app_email_from'))
            ->to($quote->getClient()->getEmail())
            ->subject('Devis #' . $quote->getQuoteNumber())
            ->html($emailHtml);
            // ->attach($pdfContent, 'devis-' . $quote->getQuoteNumber() . '.pdf', 'application/pdf');

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            // Log l'erreur
            return false;
        }
    }

    /**
     * Envoie un email avec une facture en pièce jointe
     */
    public function sendInvoiceEmail(Invoice $invoice, array $customVars = []): bool
    {
        // Configurer et générer le PDF avec Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);
        
        // Générer le HTML pour le PDF
        $pdfHtml = $this->twig->render('invoice/pdf.html.twig', [
            'invoice' => $invoice
        ]);
        
        // Charger le HTML et générer le PDF
        $dompdf->loadHtml($pdfHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();
    
        // Variables pour le template email
        $variables = [
            'invoice' => $invoice,
            'app_url' => $this->params->get('app_url'),
            'company_name' => $invoice->getCompany()->getName(),
        ];
    
        // Ajouter les variables personnalisées
        $variables = array_merge($variables, $customVars);
    
        // Générer le HTML pour l'email
        $emailHtml = $this->twig->render('invoice/email.html.twig', $variables);
    
        // Créer et envoyer l'email avec le PDF en pièce jointe
        $email = (new Email())
            ->from($this->params->get('app_email_from'))
            ->to($invoice->getClient()->getEmail())
            ->subject('Facture #' . $invoice->getInvoiceNumber())
            ->html($emailHtml)
            ->attach($pdfContent, 'facture-' . $invoice->getInvoiceNumber() . '.pdf', 'application/pdf');
    
        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            // Log l'erreur
            error_log("Erreur d'envoi de la facture par email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie une relance pour les factures impayées
     */
    public function sendPaymentReminder(Invoice $invoice): bool
    {
        // Variables spécifiques pour les relances
        $customVars = [
            'is_reminder' => true,
            'days_late' => $this->getDaysLate($invoice)
        ];

        return $this->sendInvoiceEmail($invoice, $customVars);
    }

    private function getDaysLate(Invoice $invoice): int
    {
        $today = new \DateTime();
        $dueDate = $invoice->getDateDue();

        return $today->diff($dueDate)->days;
    }

    /**
     * Envoie un email d'invitation à rejoindre une entreprise
     */
    public function sendInvitationEmail(Invitation $invitation): bool
    {
        $company = $invitation->getCompany();

        // Variables pour le template
        $variables = [
            'invitation' => $invitation,
            'company' => $company,
            'app_url' => $this->params->get('app_url'),
        ];

        $emailHtml = $this->twig->render('invitation/email.html.twig', $variables);

        $email = (new Email())
            ->from($this->params->get('app_email_from'))
            ->to($invitation->getEmail())
            ->subject('Invitation à rejoindre ' . $company->getName())
            ->html($emailHtml);

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            // Log l'erreur avec plus de détails
            error_log("Erreur d'envoi d'email: " . $e->getMessage());
            return false;
        }
    }
}