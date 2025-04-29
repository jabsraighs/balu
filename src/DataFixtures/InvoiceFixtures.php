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
        
        // Récupérer tous les devis acceptés
        $quotes = $manager->getRepository(Quote::class)->findBy(['status' => 'accepted']);
        $invoiceNumber = 1;
        
        foreach ($quotes as $quote) {
            // Création d'une facture à partir d'un devis accepté
            $createdAt = new \DateTimeImmutable($quote->getDateCreated()->format('Y-m-d H:i:s'));
            $createdAt->modify('+' . rand(2, 7) . ' days');
            
            $invoice = new Invoice();
            $invoice->setInvoiceNumber('FAC-' . str_pad($invoiceNumber, 5, '0', STR_PAD_LEFT))
                   ->setStatus(rand(1, 100) <= 80 ? 'paid' : 'pending') // 80% des factures sont payées
                   ->setDateDue($createdAt->modify('+30 days'))
                   ->setTotalAmount($quote->getTotalAmount())
                   ->setCompany($quote->getCompany())
                   ->setClient($quote->getClient())
                   ->setCustomer($quote->getCustomer())
                   ->setCreatedAt($createdAt);
            
            // Si la facture est payée, définir une date de paiement
            if ($invoice->getStatus() === 'paid') {
                $invoice->setDatePaid($createdAt->modify('+' . rand(1, 25) . ' days'));
            }
            
            $manager->persist($invoice);
            $this->addReference('invoice-' . $invoiceNumber, $invoice);
            
            // Copier les lignes du devis vers la facture
            foreach ($quote->getQuoteLines() as $quoteLine) {
                $invoiceLine = new InvoiceLine();
                $invoiceLine->setDescription($quoteLine->getDescription())
                           ->setQuantity($quoteLine->getQuantity())
                           ->setUnitPrice($quoteLine->getUnitPrice())
                           ->setProductName($quoteLine->getProductName())
                           ->setProductDescription($quoteLine->getProductDescription())
                           ->setInvoice($invoice);
                
                $manager->persist($invoiceLine);
            }
            
            $invoiceNumber++;
        }
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            QuoteFixtures::class,
            QuoteLineFixtures::class,
        ];
    }
}
