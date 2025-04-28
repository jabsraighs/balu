<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CompanyFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $date = new \DateTimeImmutable();
        
        $users = $manager->getRepository(User::class)->findBy([], null, 10); // récupère 10 utilisateurs
        
        for ($i = 0; $i < 5; $i++) { // crée 5 entreprises
            $company = (new Company())
                ->setName($faker->company)
                ->setSiret($faker->numerify('### ### ###'))
                ->setAddress($faker->address);

            $manager->persist($company);

            // Associer un user à cette entreprise
            if (isset($users[$i])) {
                $user = $users[$i];
                $user->setCompany($company); // Associe l'utilisateur à la compagnie
                $company->addUser($user); // (optionnel car `addUser` fait déjà le setCompany, selon ta définition)

                $manager->persist($user);
            }

            // Tu peux aussi ajouter d'autres utilisateurs (partenaires)
            foreach ($users as $partenaire) {
                if (!in_array('ROLE_ACCOUNTANT', $partenaire->getRoles()) && !in_array('ROLE_COMPANY', $partenaire->getRoles())) {
                    $partenaire->setRoles(['ROLE_COMPANY_ENTREPRISE']);
                    $partenaire->setCompany($company);
                    $company->addUser($partenaire);

                    $manager->persist($partenaire);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
