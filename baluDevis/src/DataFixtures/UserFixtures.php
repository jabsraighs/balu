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
        $object = (new User())
            ->setEmail($faker->email())
            ->setRoles([])
            ->setPassword($hashedPassword)
            ->setCreatedAt($date);
        $manager->persist($object);
        $this->addReference('user', $object);

        $object = (new User())
            ->setEmail('admin@gmail.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
            ->setPassword($hashedPassword)
            ->setCreatedAt($date);
        $manager->persist($object);
        $this->addReference('admin', $object);

         $isVerified= [false,true];
        for ($i = 0; $i < 10; $i++) {
            $object = (new User())
                ->setEmail($faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword($hashedPassword)
                 ->setIsVerified($isVerified[array_rand($isVerified)])
                ->setCreatedAt($date);
            $manager->persist($object);
        }

        $manager->flush();
    }
}
