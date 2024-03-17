<?php
namespace App\Service;

use App\Entity\Quote;
use App\Entity\User;
use App\Entity\Invoice;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use Symfony\Bundle\SecurityBundle\Security;

class CalculService {

    private $quoteRepository;
    private $invoiceRepository;
    private $security;
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository, QuoteRepository $quoteRepository, InvoiceRepository $invoiceRepository, Security $security)
    {
        $this->quoteRepository = $quoteRepository;
        $this->clientRepository = $clientRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->security = $security;
    }

    // Add methods for calculations and other operations related to quotes
    public function calculQuote(Quote $quote, User $user): Quote {
        $quoteLines = $quote->getQuoteLines(); // Removed $this-> before $quote
        $totalTva = 0;

        // Calculate subTotal for each QuoteLine and total TVA
        foreach ($quoteLines as $quoteLine) {
            $subTotal = $quoteLine->getQuantity() * $quoteLine->getUnitPrice();
            $quoteLine->setSubTotal($subTotal);
            $totalTva += $subTotal * $quote->getTva(); // Removed $this-> before $quote
        }

        $totalAmount = 0;
        // Calculate totalAmount for the entire Quote
        foreach ($quoteLines as $quoteLine) {
            $totalAmount += $quoteLine->getSubTotal() + $totalTva;
        }
        // Set totalAmount and totalTva for the Quote
        $quote->setTotalTva($totalTva); // Removed $this-> before $quote
        $quote->setTotalAmount($totalAmount); // Removed $this-> before $quote
        $quote->setUserQuote($user);

        return $quote;
    }
    public function calculInvoice (Invoice $invoice,User $user) {
        foreach ($invoice->getQuoteLines() as $quoteLine) {
            // Calculate subTotal for each QuoteLine (ht per item)
            $subTotal = $quoteLine->getQuantity() * $quoteLine->getUnitPrice();
            $quoteLine->setSubTotal($subTotal);
        }
        // Calculate totalAmount for the entire Invoice
        $quoteLines = $invoice->getQuoteLines() ;
        $totalTva = 0;
        foreach ($quoteLines as $quoteLine) {
            // total tva = total ht * tva
            $totalTva += $quoteLine->getSubTotal() * $invoice->getTva();
        }
        $totalAmount = 0;

        foreach ($quoteLines as $quoteLine) {
            $totalAmount += $quoteLine->getSubTotal() + $totalTva ;
        }
        // Set totalAmount for the Invoice
        $invoice->setTotalTva($totalTva);
        $invoice->setTotalAmount($totalAmount);
        $invoice->setUserInvoice($user);
    }


    public function monthlyRevenue(): float
    {
        $user = $this->security->getUser();
        $monthlyRevenue = $this->invoiceRepository->getMonthlyRevenueByUser($user);

        return $monthlyRevenue ?? 0;
    }

    public function acceptedQuotes(): float
    {
        $user = $this->security->getUser();
        $acceptedQuotes = $this->quoteRepository->findQuotesAcceptedByUser($user);

        return count($acceptedQuotes);
    }

    public function conversionRate()
    {
        $user = $this->security->getUser();
        $totalQuotes = count($this->quoteRepository->findBy(["userQuote" => $user]));
        $totalInvoices = count($this->invoiceRepository->findBy(["userInvoice" => $user]));

        if ($totalQuotes > 0) {
            $conversionRate = $totalInvoices / $totalQuotes;
        } else {
            $conversionRate = 0;
        }

        return number_format($conversionRate, 1);
    }

    public function newClients()
    {
        $user = $this->security->getUser();
        $newClients = $this->clientRepository->getNewClientsByUser($user);

        return count($newClients);
    }

    public function getAnnualAndMonthlyRevenue()
    {
        $user = $this->security->getUser();
        $annualRevenue = $this->invoiceRepository->getAnnualRevenueByUser($user);
        $revenuePerMonth = $this->invoiceRepository->getEveryMonthlyRevenue($user);

        return ['annualRevenue' => $annualRevenue, 'revenuePerMonth' => $revenuePerMonth];
    }
}