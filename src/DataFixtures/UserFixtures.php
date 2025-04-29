<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    // Définition des utilisateurs principaux
    private const ADMIN_USER = [
        'email' => 'admin@balucrm.fr',
        'roles' => ['ROLE_ADMIN'],
        'fullname' => 'Lucas Martin',
        'reference' => 'admin-user'
    ];
    
    private const COMPANY_USER = [
        'email' => 'directeur@entreprise-demo.fr',
        'roles' => ['ROLE_COMPANY'],
        'fullname' => 'Sophie Dubois',
        'reference' => 'company-user'
    ];
    
    private const ACCOUNTANT_USER = [
        'email' => 'comptable@entreprise-demo.fr',
        'roles' => ['ROLE_ACCOUNTANT'],
        'fullname' => 'Thomas Leroy',
        'reference' => 'accountant-user'
    ];

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr-FR');
        $password = 'azerty123';
        
        // Création du compte administrateur
        $admin = new User();
        $admin->setEmail(self::ADMIN_USER['email'])
              ->setRoles(self::ADMIN_USER['roles'])
              ->setIsVerified(true)
              ->setFullname(self::ADMIN_USER['fullname']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
        $manager->persist($admin);
        $this->addReference(self::ADMIN_USER['reference'], $admin);
        
        // Création du compte entreprise
        $companyUser = new User();
        $companyUser->setEmail(self::COMPANY_USER['email'])
                   ->setRoles(self::COMPANY_USER['roles'])
                   ->setIsVerified(true)
                   ->setFullname(self::COMPANY_USER['fullname']);
        $companyUser->setPassword($this->passwordHasher->hashPassword($companyUser, $password));
        $manager->persist($companyUser);
        $this->addReference(self::COMPANY_USER['reference'], $companyUser);
        
        // Création du compte comptable
        $accountantUser = new User();
        $accountantUser->setEmail(self::ACCOUNTANT_USER['email'])
                      ->setRoles(self::ACCOUNTANT_USER['roles'])
                      ->setIsVerified(true)
                      ->setFullname(self::ACCOUNTANT_USER['fullname']);
        $accountantUser->setPassword($this->passwordHasher->hashPassword($accountantUser, $password));
        $manager->persist($accountantUser);
        $this->addReference(self::ACCOUNTANT_USER['reference'], $accountantUser);
        
        for ($i = 0; $i < 8; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail())
                 ->setRoles([])
                 ->setIsVerified(true)
                 ->setFullname($faker->name());
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $manager->persist($user);
            $this->addReference('user-' . $i, $user);
        }

        $manager->flush();
    }
}
