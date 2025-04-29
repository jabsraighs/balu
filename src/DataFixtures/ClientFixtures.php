<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Company;
use App\DataFixtures\CompanyFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $mainCompany = $this->getReference('main-company', Company::class);
        
        $companyNames = [
            'Carrefour France', 'Orange SA', 'Renault Groupe', 'BNP Paribas', 'AXA Assurances',
            'Société Générale', 'Total Energies', 'SNCF', 'EDF', 'Veolia',
            'Bouygues Télécom', 'Crédit Agricole', 'Michelin', 'L\'Oréal', 'Air France-KLM',
            'Danone', 'Accor Hotels', 'Peugeot SA', 'Vivendi', 'Chanel'
        ];
        
        for ($i = 0; $i < 20; $i++) {
            $client = new Client();
            $client->setName($companyNames[$i] ?? $faker->company())
                  ->setContactName($faker->name())
                  ->setEmail($faker->companyEmail())
                  ->setPhone($faker->phoneNumber())
                  ->setAddress($faker->address())
                  ->setCompany($mainCompany)
                  ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisYear()));

            $manager->persist($client);
            $this->addReference('client-' . $i, $client);
        }

        $otherCompanies = [$this->getReference('company-0', Company::class), $this->getReference('company-1', Company::class)];
        
        for ($i = 0; $i < 15; $i++) {
            $client = new Client();
            $client->setName($faker->company())
                  ->setContactName($faker->name())
                  ->setEmail($faker->companyEmail())
                  ->setPhone($faker->phoneNumber())
                  ->setAddress($faker->address())
                  ->setCompany($otherCompanies[array_rand($otherCompanies)])
                  ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisYear()));

            $manager->persist($client);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
        ];
    }
}
