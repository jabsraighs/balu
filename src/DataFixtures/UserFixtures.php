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
        $roles = ["ROLE_COMPANY", "ROLE_ACCOUNTANT"];
        $date = new \DateTimeImmutable();

        // Création d'un utilisateur avec un fullname
        $object = (new User())
            ->setEmail('azerty@gmail.com')
            ->setRoles(['ROLE_COMPANY'])
            ->setIsVerified(true)
            ->setFullname('John Doe'); // Ajout du fullname

        $object->setPassword($this->passwordHasher->hashPassword($object, $password));
        $manager->persist($object);
        $this->addReference('user', $object);

        // Création d'un autre utilisateur avec un fullname
        $object = (new User())
            ->setEmail('admin@test.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
            ->setFullname('Admin User'); // Ajout du fullname

        $object->setPassword($this->passwordHasher->hashPassword($object, $password));
        $manager->persist($object);

        // Création de plusieurs utilisateurs avec un fullname généré
        for ($i = 0; $i < 15; $i++) {
            $user = (new User())
                ->setEmail($faker->email())
                ->setRoles([$roles[array_rand($roles)]])
                ->setIsVerified($isVerified[array_rand($isVerified)])
                ->setFullname($faker->name()); // Ajout du fullname généré

            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $manager->persist($user);
        }

        // Création d'autres utilisateurs sans rôle spécifique, mais avec un fullname
        for ($i = 0; $i < 60; $i++) {
            $user = (new User())
                ->setEmail($faker->email())
                ->setRoles([])
                ->setIsVerified($isVerified[array_rand($isVerified)])
                ->setFullname($faker->name()); // Ajout du fullname généré

            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
