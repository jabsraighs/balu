<?php
namespace App\Service;

use App\Entity\Quote;
use App\Entity\User;
use App\Entity\Invoice;

class CalculService {

    public function calculQuote(Quote $quote, User $user): Quote {
        $quoteLines = $quote->getQuoteLines(); 
        $totalTva = 0;

        // Calculate subTotal for each QuoteLine and total TVA
        foreach ($quoteLines as $quoteLine) {
            $subTotal = $quoteLine->getQuantity() * $quoteLine->getUnitPrice();
            $quoteLine->setSubTotal($subTotal);
            $totalTva += $subTotal * $quote->getTva(); 
        }

        $totalAmount = 0;
        // Calculate totalAmount for the entire Quote
        foreach ($quoteLines as $quoteLine) {
            $totalAmount += $quoteLine->getSubTotal() + $totalTva;
        }
        if(in_array('ROLE_ACCOUNTANT',$user->getRoles())) {
            $entreprise = $user->getEntreprise();
            $quote->setEntreprise($entreprise);
            $quote->setClient($quote->getClient()); 
            $quote->setEntreprise($entreprise);
            $entreprise->addEntrepriseClient($quote->getClient());
            $quote->setTotalTva($totalTva); 
            $quote->setTotalAmount($totalAmount);

        } else {
            // Set totalAmount and totalTva for the Quote
            $quote->setTotalTva($totalTva); 
            $quote->setTotalAmount($totalAmount); 
            $quote->setUserQuote($user);
        }
       

        return $quote;
    }
    public function calculInvoice (Invoice $invoice,User $user): invoice {
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
        if(in_array('ROLE_ACCOUNTANT',$user->getRoles())) {
            $entreprise = $user->getEntreprise();
            $invoice->setEntreprise($entreprise);
            $invoice->setClient($invoice->getClient()); 
            $invoice->setEntreprise($entreprise);
            $entreprise->addEntrepriseClient($invoice->getClient());
            $invoice->setTotalTva($totalTva);
            $invoice->setTotalAmount($totalAmount);

        } else {
        // Set totalAmount for the Invoice
            $invoice->setTotalTva($totalTva);
            $invoice->setTotalAmount($totalAmount);
            $invoice->setUserInvoice($user);
        }
        return $invoice;
    }
}