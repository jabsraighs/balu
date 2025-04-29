<?php

namespace App\DataFixtures;

use App\Entity\Payment;
use App\Entity\Invoice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupérer toutes les factures payées
        $invoices = $manager->getRepository(Invoice::class)->findBy(['status' => 'paid']);
        
        $paymentMethods = [
            'credit_card' => 60,    // 60% cartes bancaires
            'bank_transfer' => 30,  // 30% virements bancaires
            'paypal' => 10          // 10% PayPal
        ];
        
        foreach ($invoices as $invoice) {
            // Création d'un paiement pour chaque facture payée
            $payment = new Payment();
            $payment->setInvoice($invoice)
                   ->setAmount($invoice->getTotalAmount())
                   ->setDatePaid($invoice->getDatePaid())
                   ->setMethod($this->getRandomMethod($paymentMethods));
            
            $manager->persist($payment);
        }
        
        $manager->flush();
    }
    
    private function getRandomMethod(array $methods): string
    {
        $rand = mt_rand(1, 100);
        $cumulative = 0;
        
        foreach ($methods as $method => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $method;
            }
        }
        
        return array_key_first($methods);
    }
    
    public function getDependencies(): array
    {
        return [
            InvoiceFixtures::class,
        ];
    }
}