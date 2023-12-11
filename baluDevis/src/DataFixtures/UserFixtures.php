<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        $password = 'azerty';
        $date = new \DateTimeImmutable();
        $object = (new User())
            ->setEmail($faker->email())
            ->setRoles(['ROLE_USER'])
            ->setPassword($password)
            ->$user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            )
            ->setCreatedAt($date);
        $manager->persist($object);
        $this->addReference('user', $object);

        $object = (new User())
            ->setEmail($faker->email())
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($password)
            ->setCreatedAt($date);
        $manager->persist($object);
        $this->addReference('admin', $object);

        for ($i = 0; $i < 10; $i++) {
            $object = (new User())
                ->setEmail($faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword($password)
                ->setCreatedAt($date);
            $manager->persist($object);
        }

        $manager->flush();
    }
}
