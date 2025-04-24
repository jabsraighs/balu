<?php

namespace App\DataFixtures;

use App\Entity\Entreprise;
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
        $roles = ["ROLE_AUTO_ENTREPRENEUR","ROLE_COMPTABLE"];
        $date = new \DateTimeImmutable();
        $object = (new User())
            ->setEmail('azerty@gmail.com')
            ->setRoles(['ROLE_AUTO_ENTREPRENEUR'])
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

        for ($i = 0; $i < 15; $i++) {
            $user = (new User())
                ->setEmail($faker->email())
                ->setRoles([$roles[array_rand($roles)]])
                ->setIsVerified($isVerified[array_rand($isVerified)])
                ->setCreatedAt($date);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $manager->persist($user);
        }
        for ($i = 0; $i < 60; $i++) {
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
