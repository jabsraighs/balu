<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Utilisation de Faker pour générer des données aléatoires
        $productNames = [
            "Sites Web", "Applications Web", "Boutiques en ligne", "Applications mobiles", "Applications de bureau", 
            "Applications cross-platform", "Cloud Services", "Data Analytics", "Cybersecurity Tools", 
            "CRM Software", "ERP Solutions", "Social Media Tools", "SEO Services", "Email Marketing Software", 
            "Content Management Systems", "E-commerce Solutions", "Project Management Tools", 
            "Video Conferencing Software", "Graphic Design Tools", "Web Hosting Services"
        ];

        // Récupérer toutes les catégories et les entreprises existantes
        $categories = $manager->getRepository(Category::class)->findAll();
        $companies = $manager->getRepository(Company::class)->findAll();

        if (empty($categories) || empty($companies)) {
            throw new \Exception("Veuillez d'abord charger des catégories et des entreprises.");
        }

        // Créer 100 produits
        for ($i = 0; $i < 100; $i++) {
            $product = new Product();
            $product->setName($productNames[array_rand($productNames)]) // Nom du produit aléatoire
                    ->setDescription($faker->paragraph) // Description générée aléatoirement
                    ->setUnitPrice($faker->randomFloat(2, 10, 1000)) // Prix unitaire entre 10 et 1000
                    ->setCategory($categories[array_rand($categories)]) // Sélectionner une catégorie aléatoire
                    ->setCompany($companies[array_rand($companies)]); // Lier une entreprise aléatoire

            $manager->persist($product); // Persist du produit
        }

        $manager->flush(); // Sauvegarder tous les produits
    }

    /**
     * Dépendances pour charger les fixtures dans le bon ordre
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class, // Charge les catégories en premier
            CompanyFixtures::class,  // Charge les entreprises en second
        ];
    }
}
