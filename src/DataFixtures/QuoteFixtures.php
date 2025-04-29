<?php

namespace App\DataFixtures;

use App\Entity\Quote;
use App\Entity\Client;
use App\Entity\User;
use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void    
    {
        $faker = Factory::create('fr_FR');
        $status = ['pending', 'accepted', 'rejected'];
        
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
            $quote->setQuoteNumber('DEV-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT))
                ->setDateCreated($createdAt)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($createdAt))
                ->setDateValidUntil($expiredAt)
                ->setStatus($status[array_rand($status)])
                ->setTotalAmount($faker->randomFloat(2, 0, 1000))
                ->setCompany($companies[array_rand($companies)])
                ->setClient($clients[array_rand($clients)])
                ->setCustomer($users[array_rand($users)]);
            
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
            ClientFixtures::class,    // Charge les clients en premier
            UserFixtures::class,      // Charge les utilisateurs en second
            CompanyFixtures::class,   // Charge les entreprises en troisième
        ];
    }
}