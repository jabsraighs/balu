<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class ClientFixtures extends Fixture implements DependentFixtureInterface {



    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        //$date = new \DateTimeImmutable();
        $users = $manager->getRepository(User::class)->findAll();
        $object = (new Client())
            ->setEmail($faker->email())
            //  ->setCreatedAt($date);
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setAddress($faker->address())
            ->setPhone($faker->phoneNumber())
            ->setUserClient($users[array_rand($users)]);

            $manager->persist($object);
        $this->addReference('client', $object);

        for ($i = 0; $i < 100; $i++) {
            $object = (new Client())
            ->setEmail($faker->email())
            //  ->setCreatedAt($date);
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setAddress($faker->address())
            ->setPhone($faker->phoneNumber())
            ->setUserClient($users[array_rand($users)]);
             $manager->persist($object);
        }

        $manager->flush();
    }
     public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}

