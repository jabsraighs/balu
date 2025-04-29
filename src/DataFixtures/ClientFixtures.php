<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Company;
use App\DataFixtures\CompanyFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $date = new \DateTimeImmutable(); // Date de création

        // Récupérer toutes les entreprises déjà créées
        $companies = $manager->getRepository(Company::class)->findAll();

        if (empty($companies)) {
            throw new \Exception("Aucune entreprise n'a été trouvée. Veuillez d'abord ajouter des entreprises.");
        }

        // Créer un premier client
        $client = (new Client())
            ->setName($faker->company()) // Le nom du client peut être un nom d'entreprise ou autre
            ->setContactName($faker->name())
            ->setEmail($faker->email())
            ->setPhone($faker->phoneNumber())
            ->setAddress($faker->address())
            ->setCompany($companies[array_rand($companies)]) // Associer aléatoirement une entreprise
            ->setCreatedAt($date); // Date de création du client

        $manager->persist($client);
        $this->addReference('client', $client);

        // Créer 100 autres clients
        for ($i = 0; $i < 100; $i++) {
            $client = (new Client())
                ->setName($faker->company()) // Le nom du client
                ->setContactName($faker->name()) // Contact du client
                ->setEmail($faker->email())
                ->setPhone($faker->phoneNumber())
                ->setAddress($faker->address())
                ->setCompany($companies[array_rand($companies)]) // Associer aléatoirement une entreprise
                ->setCreatedAt($date);

            $manager->persist($client);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class, // Assurer que les entreprises existent avant
        ];
    }
}
