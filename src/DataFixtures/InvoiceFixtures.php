<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Entity\Quote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Récupérer tous les devis
        $quotes = $manager->getRepository(Quote::class)->findAll();
        $invoiceNumber = 1;
        
        foreach ($quotes as $quote) {
            // Convertir les devis acceptés à 100% et les autres à 30%
            $convertToInvoice = ($quote->getStatus() === 'accepted') || $faker->boolean(30);
            
            if ($convertToInvoice) {
                // Mettre à jour le statut du devis s'il est converti
                if ($quote->getStatus() !== 'accepted') {
                    $quote->setStatus('accepted');
                    $manager->persist($quote);
                }
                
                // Création d'une facture 2-7 jours après le devis
                $quoteDate = $quote->getDateCreated();
                $createdAt = (new \DateTimeImmutable($quoteDate->format('Y-m-d')))
                    ->modify('+' . $faker->numberBetween(2, 7) . ' days');
                
                $invoice = new Invoice();
                $invoice->setInvoiceNumber('FAC-' . str_pad($invoiceNumber, 5, '0', STR_PAD_LEFT))
                       ->setStatus($faker->randomElement(['pending', 'paid', 'overdue']))
                       ->setDateDue($createdAt->modify('+30 days'))
                       ->setTotalAmount($quote->getTotalAmount())
                       ->setCompany($quote->getCompany())
                       ->setClient($quote->getClient())
                       ->setCustomer($quote->getCustomer())
                       ->setCreatedAt($createdAt);
                
                // Si la facture est payée, définir une date de paiement
                if ($invoice->getStatus() === 'paid') {
                    // Date entre la création et la date d'échéance (ou aujourd'hui si plus récent)
                    $now = new \DateTime();
                    $dueDate = new \DateTime($invoice->getDateDue()->format('Y-m-d'));
                    $paymentDateMax = min($now, $dueDate);
                    
                    $daysUntilPayment = $faker->numberBetween(
                        0, 
                        max(0, (int)$createdAt->diff($paymentDateMax)->format('%a'))
                    );
                    
                    if ($daysUntilPayment > 0) {
                        $paymentDate = (clone $createdAt)->modify('+' . $daysUntilPayment . ' days');
                        $invoice->setDatePaid($paymentDate);
                    } else {
                        $invoice->setDatePaid($createdAt);
                    }
                } else if ($invoice->getStatus() === 'overdue') {
                    // Pour les factures en retard, la date d'échéance est dépassée
                    $newDueDate = (clone $createdAt)->modify('-' . $faker->numberBetween(5, 20) . ' days');
                    $invoice->setDateDue($newDueDate);
                }
                
                $manager->persist($invoice);
                $this->addReference('invoice-' . $invoiceNumber, $invoice);
                
                // Copier les lignes du devis vers la facture
                foreach ($quote->getQuoteLines() as $quoteLine) {
                    $invoiceLine = new InvoiceLine();
                    $invoiceLine->setDescription($quoteLine->getDescription())
                               ->setQuantity($quoteLine->getQuantity())
                               ->setUnitPrice($quoteLine->getUnitPrice())
                               ->setDiscount($quoteLine->getDiscount())
                               ->setProductName($quoteLine->getProductName())
                               ->setProductDescription($quoteLine->getProductDescription())
                               ->setInvoice($invoice);
                    
                    $manager->persist($invoiceLine);
                }
                
                $invoiceNumber++;
            }
        }
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            QuoteFixtures::class,
        ];
    }
}
