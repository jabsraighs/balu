<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void {

        $users = $manager->getRepository(User::class)->findAll();
        $faker = \Faker\Factory::create('fr_FR');
        $date = new \DateTimeImmutable();
        $isReadTab = [false,true];
        for($i = 0 ; $i < 10 ; $i++) {
            
            $object= (new Message())
                ->setCreatedAt($date)
                ->setIsRead($isReadTab[array_rand($isReadTab)])
                ->setSender($users[array_rand($users)])
                ->setRecipient($users[array_rand($users )])
                ->setMessage($faker->paragraph(6))
                ;
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
