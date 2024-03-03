<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\Payment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;



class PaymentFixtures extends Fixture implements DependentFixtureInterface {



    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        $status = ["valider", "en cours", "refuser"];
        $invoices = $manager->getRepository(Invoice::class)->findValidInvoices();


            for ($i = 0; $i < 10; $i++) {
                $quantity = $faker->numberBetween(1, 100);
                $createdAt = $faker->dateTimeThisDecade();
                $paymentAt = $faker->dateTimeInInterval($createdAt, '+1 year');
                $payments = (new Payment())
                    ->setInvoice($invoices[array_rand($invoices)])
                    ->setPaymentDate(\DateTimeImmutable::createFromMutable($paymentAt))
                    ->setAmountPaid($quantity);
                $manager->persist($payments);
            }
        $manager->flush();
        }

    public function getDependencies()
    {
        return [
            InvoiceFixtures::class,
        ];
    }

}

