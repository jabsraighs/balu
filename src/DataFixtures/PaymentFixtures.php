<?php 

namespace App\DataFixtures;

use App\Entity\Payment;
use App\Entity\Invoice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PaymentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Créer un générateur Faker en français
        $invoices = $manager->getRepository(Invoice::class)->findAll();

        if (empty($invoices)) {
            throw new \Exception("Assurez-vous d'avoir des factures dans la base de données.");
        }

        // Créer 100 paiements
        for ($i = 0; $i < 100; $i++) {
            $invoice = $invoices[array_rand($invoices)]; // Sélectionner une facture aléatoire

            $payment = (new Payment())
                ->setInvoice($invoice) // Lier le paiement à une facture existante
                ->setAmount($faker->randomFloat(2, 50, 500)) // Montant du paiement entre 50 et 500
                ->setDatePaid($faker->dateTimeThisYear()) // Date de paiement cette année
                ->setMethod($faker->randomElement(['credit_card', 'paypal', 'bank_transfer'])); // Méthode de paiement

            $manager->persist($payment); // Enregistrer le paiement dans la base de données
        }

        $manager->flush(); // Sauvegarder les paiements
    }

    public function getDependencies(): array
    {
        return [
            InvoiceFixtures::class, // Ajout de la dépendance à InvoiceFixtures
        ];
    }
}
