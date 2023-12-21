<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture {
     private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
           // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        $password = 'azerty';
        $date = new \DateTimeImmutable();
        $hashedPassword = $this->passwordEncoder->hashPassword(
            (new User())
                ->setEmail($faker->email())
                ->setRoles([])
                ->setCreatedAt($date),
            $password
        );
        $date = new \DateTimeImmutable();
        $object = (new User())
            ->setEmail($faker->email())
            ->setRoles([])
            ->setPassword($hashedPassword)
            ->setCreatedAt($date);
        $manager->persist($object);
        $this->addReference('user', $object);

        $object = (new User())
            ->setEmail('admin@test.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($hashedPassword)
            ->setCreatedAt($date);
        $manager->persist($object);


        for ($i = 0; $i < 10; $i++) {
            $object = (new User())
                ->setEmail($faker->email())
                ->setRoles([])
                ->setPassword($hashedPassword)
                ->setCreatedAt($date);
            $manager->persist($object);
        }

        $manager->flush();
    }
}

