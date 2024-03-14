<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture {


    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {

    }

    public function load(ObjectManager $manager): void
    {
           // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        $password = 'azerty';
        $isVerified = [false,true];
        $date = new \DateTimeImmutable();

        $date = new \DateTimeImmutable();
        $object = (new User())
            ->setEmail('azerty@gmail.com')
            ->setRoles([])
            ->setIsVerified(true)
            ->setCreatedAt($date);
            $object->setPassword($this->passwordHasher->hashPassword($object, $password));
        $manager->persist($object);
        $this->addReference('user', $object);

        $object = (new User())
            ->setEmail('admin@test.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
            ->setCreatedAt($date);
            $object->setPassword($this->passwordHasher->hashPassword($object, $password));
            $manager->persist($object);


        for ($i = 0; $i < 1; $i++) {
            $object = (new User())
                ->setEmail($faker->email())
                ->setRoles([])
                ->setIsVerified($isVerified[array_rand($isVerified)])
                ->setCreatedAt($date);
            $object->setPassword($this->passwordHasher->hashPassword($object, $password));
            $manager->persist($object);
        }

        $manager->flush();
    }
}

