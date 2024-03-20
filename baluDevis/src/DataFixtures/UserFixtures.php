<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr-Fr');
        $password = 'azerty';
        $isVerified = [false, true];
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
        // Create users to serve as "entreprise"
        $selectedUsers = [];
        for ($j = 0; $j < 3; $j++) {
            $enterprise = (new User())
                ->setEmail('entreprise' . $j . '@example.com')
                ->setRoles(['ROLES_ENTREPRISE'])
                ->setIsVerified(true)
                ->setCreatedAt($date);
            $enterprise->setPassword($this->passwordHasher->hashPassword($enterprise, $password));
            $manager->persist($enterprise);
            $selectedUsers[] = $enterprise;
        }

        // Create users and assign them to the "entreprise" users
        for ($i = 0; $i < 10; $i++) {
            $user = (new User())
                ->setEmail($faker->email())
                ->setRoles(['ROLES_ENTREPRISE'])
                ->setIsVerified($isVerified[array_rand($isVerified)])
                ->setCreatedAt($date);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            
            // Randomly assign a user to one of the "entreprise" users
            $enterprise = $selectedUsers[array_rand($selectedUsers)];
            $user->setEntreprise($enterprise);
            
            $manager->persist($user);
        }
        for ($i = 0; $i < 10; $i++) {
            $user = (new User())
                ->setEmail($faker->email())
                ->setRoles([])
                ->setIsVerified($isVerified[array_rand($isVerified)])
                ->setCreatedAt($date);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
