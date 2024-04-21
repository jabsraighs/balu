<?php

namespace App\DataFixtures;

use App\Entity\Entreprise;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class EntrepriseFixtures extends Fixture implements DependentFixtureInterface {



    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        $date = new \DateTimeImmutable();
        $users = $manager->getRepository(User::class)->findBy([], null, 10);
        $user =  $manager->getRepository(User::class)->findAll();
        for ($i = 0; $i < 5; $i++) {
            $entreprise = (new Entreprise())
                ->setEmail($faker->email)
                ->setCreatedAt($date)
                ->setUserEntreprise($users[$i])
                ->setNomEntreprise($faker->company);
                
            // Définir l'utilisateur comme utilisateur créateur de l'entreprise
            $users[$i]->setUserCreateEntreprise($entreprise);
        
            $manager->persist($entreprise);
            $manager->persist($users[$i]);

            // Ajouter chaque utilisateur à l'entreprise
            foreach ($users as $partenaire) {
                $roles = $partenaire->getRoles(); // Obtient les rôles de l'utilisateur
                // Vérifie si l'utilisateur n'a pas les rôles spécifiés
                if (!in_array('ROLE_COMPTABLE', $roles) && !in_array('ROLE_AUTO_ENTREPRENEUR', $roles)) {
                    $partenaire->setRoles(['ROLE_USER_ENTREPRISE']); // Définit les rôles pour l'utilisateur
                    $entreprise->addPartenaire($partenaire); // Ajoute l'utilisateur en tant que partenaire dans l'entreprise
                }
                $manager->persist($partenaire);

            }
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

