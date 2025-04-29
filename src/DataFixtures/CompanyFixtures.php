<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CompanyFixtures extends Fixture implements DependentFixtureInterface
{
    private const MAIN_COMPANY = [
        'name' => 'Entreprise Démo SAS',
        'siret' => '1234567890',
        'address' => '15 rue de l\'Innovation, 75001 Paris',
        'reference' => 'main-company'
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        
        // Création de l'entreprise principale
        $mainCompany = new Company();
        $mainCompany->setName(self::MAIN_COMPANY['name'])
                   ->setSiret(self::MAIN_COMPANY['siret'])
                   ->setAddress(self::MAIN_COMPANY['address']);
        
        $companyUser = $this->getReference('company-user', User::class);
        $companyUser->setCompany($mainCompany);
        $mainCompany->addUser($companyUser);
        
        $accountantUser = $this->getReference('accountant-user', User::class);
        $accountantUser->setCompany($mainCompany);
        $mainCompany->addUser($accountantUser);
        
        $manager->persist($mainCompany);
        $manager->persist($companyUser);
        $manager->persist($accountantUser);
        
        $this->addReference(self::MAIN_COMPANY['reference'], $mainCompany);
        
        for ($i = 0; $i < 2; $i++) {
            $company = new Company();
            $company->setName($faker->company())
                  ->setSiret($faker->numerify('###########'))
                  ->setAddress($faker->address());
            
            $manager->persist($company);
            $this->addReference('company-' . $i, $company);
            
            for ($j = 0; $j < 3; $j++) {
                $userIndex = $i*3 + $j;
                if ($this->hasReference('user-' . $userIndex, User::class)) {
                    $user = $this->getReference('user-' . $userIndex, User::class);
                    $user->setCompany($company);
                    $company->addUser($user);
                    $manager->persist($user);
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
