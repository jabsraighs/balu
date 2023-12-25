<?php

namespace App\DataFixtures;


use App\Entity\Quote;
use App\Entity\QuoteLine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;



class QuoteLineFixtures extends Fixture implements DependentFixtureInterface {



    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        //$date = new \DateTimeImmutable();
        $quote = $manager->getRepository(Quote::class)->findAll();
        
        for ($i = 0; $i < 10; $i++) {
            $quantity = $faker->numberBetween(1, 100);
            $unitPrice = $faker->randomFloat(2,1,100);
            $quoteLines = (new QuoteLine())
                    ->setQuote($quote[array_rand($quote)])
                    ->setQuantity($quantity)
                    ->setUnitPrice($unitPrice)
                    ->setSubtotal($quantity * $unitPrice);
            $manager->persist($quoteLines);
        }
        $manager->flush();
    }
     public function getDependencies()
    {
        return [
            ClientFixtures::class,
        ];
    }

}

