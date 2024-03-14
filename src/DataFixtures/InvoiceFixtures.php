<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Invoice;
use App\Entity\Quote;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;



class InvoiceFixtures extends Fixture implements DependentFixtureInterface {



    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        $status = ["valider", "en cours", "refuser"];
        $quotes = $manager->getRepository(Quote::class)->findValidQuotes();
        $users = $manager->getRepository(User::class)->findAll();
        $clients = $manager->getRepository(Client::class)->findAll();
        $tva = [0,10,20];

            for ($i = 0; $i < 1000; $i++) {
                $quantity = $faker->numberBetween(1, 100);
                $name = "Facture numero".$i ;
                $createdAt = $faker->dateTimeThisMonth(); // Default to today's date
                if ($i % 2 == 0) {
                    // Set createdAt to 1 month later for every other invoice
                    $createdAt->modify('+1 month');
                }
                $expiredAt = $faker->dateTimeInInterval($createdAt, '+1 month');
                $invoices = (new Invoice())
                    ->setQuote($quotes[array_rand($quotes)])
                    ->setName($name)
                    ->setUserInvoice($users[array_rand($users)])
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($createdAt))
                    ->setDueDate(\DateTimeImmutable::createFromMutable($expiredAt))
                    ->setTotalAmount($quantity)
                    ->setClient($clients[array_rand($clients)])
                    ->setTva($tva[array_rand($tva)])
                    ->setTotalTva($faker->randomFloat(2,0,1000))
           
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

