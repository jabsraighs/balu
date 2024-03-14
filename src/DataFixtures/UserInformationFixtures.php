<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserInformation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserInformationFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr-Fr');
        $users = $manager->getRepository(User::class)->findAll();
        for ($i = 0; $i < count($users); $i++) {
            $user = $users[$i];
            $object = (new UserInformation())
                ->setEmail($faker->email())
                //  ->setCreatedAt($date);
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setPhone($faker->phoneNumber())
                ->setUser($user);
    
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
