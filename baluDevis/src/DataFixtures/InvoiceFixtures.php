<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\Quote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;



class InvoiceFixtures extends Fixture implements DependentFixtureInterface {



    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        $status = ["valider", "en cours", "refuser"];
        $quotes = $manager->getRepository(Quote::class)->findValidQuotes();


            for ($i = 0; $i < 10; $i++) {
                $quantity = $faker->numberBetween(1, 100);
                $createdAt = $faker->dateTimeThisDecade();
                $expiredAt = $faker->dateTimeInInterval($createdAt, '+1 year');
                $invoices = (new Invoice())
                    ->setQuote($quotes[array_rand($quotes)])
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($createdAt))
                    ->setDueDate(\DateTimeImmutable::createFromMutable($expiredAt))
                    ->setTotalAmount($quantity)
                    ->setPaymentStatus($status[array_rand($status)]);


                $manager->persist($invoices);
            }
        $manager->flush();
        }

    public function getDependencies()
    {
        return [
            QuoteFixtures::class,
        ];
    }

}

