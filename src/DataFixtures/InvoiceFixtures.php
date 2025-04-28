<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\Client;
use App\Entity\Company;
use App\Entity\User;
use App\Entity\InvoiceLine;
use App\Entity\Payment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Créer un générateur Faker en français
        $date = new \DateTimeImmutable(); // Date de création

        // Récupérer des entités existantes dans la base de données
        $clients = $manager->getRepository(Client::class)->findAll();
        $companies = $manager->getRepository(Company::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        if (empty($clients) || empty($companies) || empty($users)) {
            throw new \Exception("Assurez-vous d'avoir des clients, des entreprises et des utilisateurs dans la base de données.");
        }

        // Créer 100 factures
        for ($i = 0; $i < 100; $i++) {
            $invoice = (new Invoice())
                ->setInvoiceNumber($faker->unique()->numerify('INV-#######')) // Générer un numéro de facture unique
                ->setStatus($faker->randomElement(['pending', 'paid', 'cancelled'])) // Statut aléatoire de la facture
                ->setDateDue($faker->dateTimeBetween('now', '+1 month')) // Date d'échéance (1 mois à partir d'aujourd'hui)
                ->setDatePaid($faker->optional()->dateTimeThisYear()) // Date de paiement (facultative)
                ->setTotalAmount($faker->randomFloat(2, 100, 1000)) // Montant total de la facture
                ->setCompany($companies[array_rand($companies)]) // Associer une entreprise aléatoire
                ->setClient($clients[array_rand($clients)]) // Associer un client aléatoire
                ->setCustomer($users[array_rand($users)]) // Associer un utilisateur (créateur de la facture)
                ->setCreatedAt($date); // Date de création de la facture

            // Créer des lignes de facture
            for ($j = 0; $j < 3; $j++) { // Par exemple, 3 lignes de facture par facture
                $invoiceLine = (new InvoiceLine())
                    ->setDescription($faker->sentence()) // Description de la ligne
                    ->setQuantity($faker->numberBetween(1, 5)) // Quantité
                    ->setUnitPrice($faker->randomFloat(2, 10, 100)) // Prix unitaire
                    ->setInvoice($invoice); // Lier la ligne de facture à la facture

                $manager->persist($invoiceLine);
            }

            // Créer un paiement associé (facultatif)
            if ($faker->boolean(50)) { // 50% de chance qu'une facture ait un paiement
                $payment = (new Payment())
                    ->setAmount($faker->randomFloat(2, 50, 500)) // Montant du paiement
                    ->setDatePaid($faker->dateTimeThisYear()) // Date du paiement
                    ->setInvoice($invoice); // Lier le paiement à la facture

                $manager->persist($payment);
            }

            $manager->persist($invoice); // Enregistrer la facture dans la base de données
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,    // Les clients doivent être créés avant les factures
            CompanyFixtures::class,   // Les entreprises doivent être créées avant les factures
            UserFixtures::class,      // Les utilisateurs doivent être créés avant les factures
        ];
    }
}