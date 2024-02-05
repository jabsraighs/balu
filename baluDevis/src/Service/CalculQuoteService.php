<?php
namespace App\Service;

use App\Entity\Quote;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class CalculQuoteService {
    // Add methods for calculations and other operations related to quotes
    public function calculate(Quote $quote, User $user,EntityManagerInterface $em): Quote {
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
}
