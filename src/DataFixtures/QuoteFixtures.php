<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Quote;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;



class QuoteFixtures extends Fixture implements DependentFixtureInterface {



    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        //$date = new \DateTimeImmutable();
        $status = ["valider","en cours","refuser"];
        $tva = [0,10,20];
        
        $clients = $manager->getRepository(Client::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 100; $i++) {
            $createdAt = $faker->dateTimeThisDecade();
            $name = "Devis numero".$i ;
            $expiredAt = $faker->dateTimeInInterval($createdAt, '+1 year');
            $quotes = (new Quote())
                    ->setDescription($faker->paragraph())
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($createdAt))
                    ->setExpiryAt(\DateTimeImmutable::createFromMutable($expiredAt))
                    ->setStatus($status[array_rand($status)])
                    ->setClient($clients[array_rand($clients)])
                    ->setName($name)
                    ->setTva($tva[array_rand($tva)])
                    ->setTotalTva($faker->randomFloat(2,0,1000))
                    ->setTotalAmount($faker->randomFloat(2,0,1000))
                    ->setUserQuote($users[array_rand($users)]);
            $manager->persist($quotes);
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

