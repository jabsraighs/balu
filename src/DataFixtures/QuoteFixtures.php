<?php

namespace App\DataFixtures;

use App\Entity\Quote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $mainCompany = $this->getReference('main-company', \App\Entity\Company::class);
        $companyUser = $this->getReference('company-user', \App\Entity\User::class);
        
        $status = ['draft', 'sent', 'accepted', 'rejected', 'expired'];
        $statusDistribution = [15, 30, 35, 10, 10];
        
        $monthlyDistribution = [
            '01' => 5,  // Janvier
            '02' => 6,  // Février
            '03' => 7,  // Mars
            '04' => 8,  // Avril
            '05' => 9,  // Mai
            '06' => 10, // Juin 
            '07' => 9,  // Juillet
            '08' => 6,  // Août
            '09' => 12, // Septembre
            '10' => 14, // Octobre
            '11' => 7,  // Novembre
            '12' => 7   // Décembre
        ];
        
        $quoteNumber = 1;
        $year = 2024;
        
        foreach ($monthlyDistribution as $month => $count) {
            for ($i = 0; $i < $count; $i++) {
                // Générer une date dans le mois concerné
                $day = str_pad($faker->numberBetween(1, 28), 2, '0', STR_PAD_LEFT);
                $dateString = "$year-$month-$day";
                $createdAt = \DateTimeImmutable::createFromFormat('Y-m-d', $dateString);
                
                // Date d'expiration (30 jours après création)
                $expiredAt = $createdAt->modify('+30 days');
                
                // Déterminer le statut selon la distribution souhaitée
                $randomValue = $faker->numberBetween(1, 100);
                $cumulativeProb = 0;
                $selectedStatus = $status[0];
                
                foreach ($statusDistribution as $index => $probability) {
                    $cumulativeProb += $probability;
                    if ($randomValue <= $cumulativeProb) {
                        $selectedStatus = $status[$index];
                        break;
                    }
                }
                
                // Sélection aléatoire d'un client
                $clientIndex = $faker->numberBetween(0, 19);
                $client = $this->getReference('client-' . $clientIndex, \App\Entity\Client::class);
                
                // Création du devis
                $quote = new Quote();
                $quote->setQuoteNumber('DEV-' . str_pad($quoteNumber, 5, '0', STR_PAD_LEFT))
                    ->setDateCreated($createdAt)
                    ->setCreatedAt($createdAt)
                    ->setDateValidUntil($expiredAt)
                    ->setStatus($selectedStatus)
                    ->setCompany($mainCompany)
                    ->setClient($client)
                    ->setCustomer($companyUser)
                    ->setTotalAmount(0);
                
                $manager->persist($quote);
                $this->addReference('quote-' . $quoteNumber, $quote);
                $quoteNumber++;
            }
        }
        
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
            CompanyFixtures::class,
            UserFixtures::class,
        ];
    }
}