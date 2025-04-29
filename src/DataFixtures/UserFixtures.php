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

    // Vérifier si l'utilisateur existe déjà
    $existingUser = $manager->getRepository(User::class)->findOneBy(['email' => 'azerty@gmail.com']);
    
    if (!$existingUser) {
        // Création d'un utilisateur avec un fullname
        $object = (new User())
            ->setEmail('azerty@gmail.com')
            ->setRoles(['ROLE_COMPANY'])
            ->setIsVerified(true)
            ->setFullname('John Doe');

        $object->setPassword($this->passwordHasher->hashPassword($object, $password));
        $manager->persist($object);
        $this->addReference('user', $object);
    } else {
        // Si l'utilisateur existe déjà, utiliser cet utilisateur comme référence
        $this->addReference('user', $existingUser);
    }

    // Faire la même vérification pour l'admin
    $existingAdmin = $manager->getRepository(User::class)->findOneBy(['email' => 'admin@test.com']);
    
    if (!$existingAdmin) {
        $object = (new User())
            ->setEmail('admin@test.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
            ->setFullname('Admin User');

        $object->setPassword($this->passwordHasher->hashPassword($object, $password));
        $manager->persist($object);
    }

    // Pour les utilisateurs générés, utiliser unique() pour éviter les doublons
    for ($i = 0; $i < 15; $i++) {
        $email = $faker->unique()->email();
        $existingUser = $manager->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$existingUser) {
            $user = (new User())
                ->setEmail($email)
                ->setRoles([$roles[array_rand($roles)]])
                ->setIsVerified($isVerified[array_rand($isVerified)])
                ->setFullname($faker->name());

            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $manager->persist($user);
        }
    }

    // Pareil pour les autres utilisateurs
    for ($i = 0; $i < 60; $i++) {
        $email = $faker->unique()->email();
        $existingUser = $manager->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$existingUser) {
            $user = (new User())
                ->setEmail($email)
                ->setRoles([])
                ->setIsVerified($isVerified[array_rand($isVerified)])
                ->setFullname($faker->name());

            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $manager->persist($user);
        }
    }

    $manager->flush();
    }
}
