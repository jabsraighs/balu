<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Quote;
use App\Entity\User;
use App\Entity\Company; // Ajout de l'entité Company
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class QuoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Utilisation de Faker pour générer des données aléatoires
        
        // Statuts possibles pour un devis
        $status = ["draft", "valider", "en cours", "refuser"];
        
        // Liste des clients et utilisateurs à récupérer depuis la base de données
        $clients = $manager->getRepository(Client::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();
        $companies = $manager->getRepository(Company::class)->findAll();

        if (empty($clients) || empty($users) || empty($companies)) {
            throw new \Exception("Veuillez d'abord charger des clients, des utilisateurs et des entreprises.");
        }

        for ($i = 0; $i < 100; $i++) {
            $createdAt = $faker->dateTimeThisDecade();
            $name = "Devis numéro " . ($i + 1);
            $expiredAt = $faker->dateTimeInInterval($createdAt, '+1 year');

            // Création d'un devis
            $quote = new Quote();
            $quote->setQuoteNumber($faker->unique()->word) // Numéro de devis unique
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($createdAt)) // Date de création du devis
                ->setDateValidUntil($expiredAt) // Date de validité jusqu'à
                ->setStatus($status[array_rand($status)]) // Statut du devis
                ->setTotalAmount($faker->randomFloat(2, 0, 1000)) // Montant total
                ->setCompany($companies[array_rand($companies)]) // Sélection d'une entreprise aléatoire
                ->setClient($clients[array_rand($clients)]) // Sélection d'un client aléatoire
                ->setCustomer($users[array_rand($users)]) // Sélection d'un utilisateur (client du devis)
                ->setQuoteNumber('DEV-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT)); // Numéro de devis formaté

            // Persist du devis
            $manager->persist($quote);
        }

        // Sauvegarde des devis dans la base de données
        $manager->flush();
    }

    /**
     * Dépendances pour charger les fixtures dans le bon ordre
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            ClientFixtures::class, // Charge les clients en premier
            UserFixtures::class,   // Charge les utilisateurs en second
            CompanyFixtures::class, // Charge les entreprises en troisième
        ];
    }
}
