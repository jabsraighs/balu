<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Invoice;
use App\Entity\Quote;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface {



    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $status = ["valider", "en cours", "refuser"];
        $quotes = $manager->getRepository(Quote::class)->findValidQuotes();
        $users = $manager->getRepository(User::class)->findAll();
        $clients = $manager->getRepository(Client::class)->findAll();
        $tva = [0, 10, 20];

        for ($month = 1; $month <= 12; $month++) {
            for ($i = 0; $i < 200; $i++) {
                $quantity = $faker->numberBetween(1, 100);
                $name = "Facture numéro " . ($i + 1);
                $createdAt = $faker->dateTimeThisMonth()->setDate(date('Y'), $month, 1);
                if ($i % 2 == 0) {
                    $createdAt->modify('+1 month');
                }
                $expiredAt = $faker->dateTimeInInterval($createdAt, '+1 month');
                $invoice = (new Invoice())
                    ->setQuote($quotes[array_rand($quotes)])
                    ->setName($name)
                    ->setUserInvoice($users[array_rand($users)])
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($createdAt))
                    ->setDueDate(\DateTimeImmutable::createFromMutable($expiredAt))
                    ->setTotalAmount($quantity)
                    ->setClient($clients[array_rand($clients)])
                    ->setTva($tva[array_rand($tva)])
                    ->setTotalTva($faker->randomFloat(2, 0, 1000))
                    ->setPaymentStatus($status[array_rand($status)]);

                $manager->persist($invoice);
            }
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

