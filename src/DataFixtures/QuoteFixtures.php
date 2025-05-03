<?php

namespace App\DataFixtures;

use App\Entity\Quote;
use App\Entity\QuoteLine;
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
        
        // Période des 6 derniers mois
        $now = new \DateTimeImmutable();
        $sixMonthsAgo = (new \DateTimeImmutable())->modify('-5 months')->modify('first day of this month');
        $quoteNumber = 1;
        
        // Générer des devis pour chaque mois des 6 derniers mois
        $currentDate = clone $sixMonthsAgo;
        while ($currentDate <= $now) {
            $monthName = $currentDate->format('F');
            $year = $currentDate->format('Y');
            $month = $currentDate->format('m');
            
            // Nombre de devis pour ce mois (entre 5 et 15)
            $quoteCount = $faker->numberBetween(5, 15);
            
            for ($i = 0; $i < $quoteCount; $i++) {
                // Générer une date dans le mois concerné
                $day = $faker->numberBetween(1, min(28, (int)$currentDate->format('t')));
                $quoteDate = \DateTimeImmutable::createFromFormat(
                    'Y-m-d',
                    sprintf('%s-%s-%02d', $year, $month, $day)
                );
                
                // Date d'expiration (30 jours après création)
                $expiredAt = $quoteDate->modify('+30 days');
                
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
                    ->setDateCreated($quoteDate)
                    ->setCreatedAt($quoteDate)
                    ->setDateValidUntil($expiredAt)
                    ->setStatus($selectedStatus)
                    ->setCompany($mainCompany)
                    ->setClient($client)
                    ->setCustomer($companyUser);
                
                // Ajouter des lignes au devis (entre 1 et 4 lignes)
                $lineCount = $faker->numberBetween(1, 4);
                $totalAmount = 0;
                
                for ($j = 0; $j < $lineCount; $j++) {
                    // Sélectionner un produit aléatoire
                    $productIndex = $faker->numberBetween(0, 9);
                    $product = $this->getReference('category-' . $productIndex, \App\Entity\Category::class);
                    
                    $quoteLine = new QuoteLine();
                    $quoteLine->setProductName('Produit #' . ($j + 1))
                            ->setProductDescription('Description du produit #' . ($j + 1))
                            ->setDescription('Prestation ' . ($j + 1))
                            ->setQuantity($faker->numberBetween(1, 5))
                            ->setUnitPrice($faker->randomFloat(2, 100, 1000))
                            ->setDiscount($faker->numberBetween(0, 15))
                            ->setQuote($quote);
                    
                    $manager->persist($quoteLine);
                    
                    // Calcul du montant
                    $lineAmount = $quoteLine->getQuantity() * $quoteLine->getUnitPrice() * (1 - $quoteLine->getDiscount() / 100);
                    $totalAmount += $lineAmount;
                }
                
                $quote->setTotalAmount((string)round($totalAmount, 2));
                
                $manager->persist($quote);
                $this->addReference('quote-' . $quoteNumber, $quote);
                $quoteNumber++;
            }
            
            // Passer au mois suivant
            $currentDate = $currentDate->modify('+1 month');
        }
        
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
            CompanyFixtures::class,
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}