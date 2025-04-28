<?php

namespace App\Command;

use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use App\Service\EmailService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-emails',
    description: 'Envoie les emails automatisés (factures impayées, rappels de devis)',
)]
class SendEmailsCommand extends Command
{
    private $invoiceRepository;
    private $quoteRepository;
    private $emailService;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        QuoteRepository $quoteRepository,
        EmailService $emailService
    ) {
        parent::__construct();
        $this->invoiceRepository = $invoiceRepository;
        $this->quoteRepository = $quoteRepository;
        $this->emailService = $emailService;
        
        // Log des dépendances injectées
        echo "SendEmailsCommand: Dépendances injectées\n";
    }

    protected function configure(): void
    {
        $this->setHelp('Cette commande envoie des emails pour les factures en retard et les devis qui expirent bientôt.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Envoi des emails automatisés');
        
        $io->writeln('DEBUG: Début de l\'exécution de la commande');
        
        $io->writeln('DEBUG: Recherche des factures en retard...');
        $overdueInvoices = $this->invoiceRepository->findOverdueInvoices();
        $io->section('Factures en retard à relancer: ' . count($overdueInvoices));
        $io->writeln('DEBUG: ' . count($overdueInvoices) . ' factures trouvées');
        
        $invoiceSuccess = 0;
        foreach ($overdueInvoices as $invoice) {
            $io->write('Envoi de rappel pour la facture #' . $invoice->getInvoiceNumber() . '... ');
            $io->writeln('DEBUG: Tentative d\'envoi pour la facture #' . $invoice->getInvoiceNumber() . ' à ' . $invoice->getClient()->getEmail());
            
            if ($this->emailService->sendPaymentReminder($invoice)) {
                $invoiceSuccess++;
                $io->writeln('<info>OK</info>');
            } else {
                $io->writeln('<error>ÉCHEC</error>');
                $io->writeln('DEBUG: Échec de l\'envoi pour la facture #' . $invoice->getInvoiceNumber());
            }
        }
        
        $io->writeln('DEBUG: Recherche des devis qui expirent bientôt...');
        $expiringQuotes = $this->quoteRepository->findExpiringQuotes();
        $io->section('Devis qui expirent bientôt: ' . count($expiringQuotes));
        $io->writeln('DEBUG: ' . count($expiringQuotes) . ' devis trouvés');
        
        $quoteSuccess = 0;
        foreach ($expiringQuotes as $quote) {
            $io->write('Envoi de rappel pour le devis #' . $quote->getQuoteNumber() . '... ');
            $daysLeft = $this->getDaysLeft($quote);
            $io->writeln('DEBUG: Devis #' . $quote->getQuoteNumber() . ' expire dans ' . $daysLeft . ' jours');
            
            $customVars = ['is_expiring_soon' => true, 'days_left' => $daysLeft];
            
            $io->writeln('DEBUG: Tentative d\'envoi pour le devis #' . $quote->getQuoteNumber() . ' à ' . $quote->getClient()->getEmail());
            if ($this->emailService->sendQuoteEmail($quote, $customVars)) {
                $quoteSuccess++;
                $io->writeln('<info>OK</info>');
            } else {
                $io->writeln('<error>ÉCHEC</error>');
                $io->writeln('DEBUG: Échec de l\'envoi pour le devis #' . $quote->getQuoteNumber());
            }
        }
        
        $overdueCount = count($overdueInvoices);
        $quoteCount = count($expiringQuotes);
        $io->success("Récapitulatif: {$invoiceSuccess}/{$overdueCount} rappels de factures et {$quoteSuccess}/{$quoteCount} rappels de devis envoyés avec succès.");
        
        $io->writeln('DEBUG: Fin de l\'exécution de la commande');
        
        return Command::SUCCESS;
    }
    
    private function getDaysLeft($quote): int
    {
        $today = new \DateTime();
        $validUntil = $quote->getDateValidUntil();
        
        $daysLeft = $today->diff($validUntil)->days;
        
        // Si la date est dépassée, on retourne 0
        if ($today > $validUntil) {
            return 0;
        }
        
        return $daysLeft;
    }
}