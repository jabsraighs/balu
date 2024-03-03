<?php
namespace App\Service;

use App\Entity\Quote;
use App\Entity\User;
use App\Entity\Invoice;

class CalculService {
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
}