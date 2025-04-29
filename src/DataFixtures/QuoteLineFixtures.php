<?php

namespace App\DataFixtures;

use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class QuoteLineFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Utilisation de Faker pour générer des données aléatoires

        // Récupération de tous les devis et produits existants
        $quotes = $manager->getRepository(Quote::class)->findAll();
        $products = $manager->getRepository(Product::class)->findAll();

        // Vérification que nous avons des devis et produits
        if (empty($quotes) || empty($products)) {
            throw new \Exception("Veuillez d'abord charger des devis et des produits.");
        }

        // Création de 200 lignes de devis
        for ($i = 0; $i < 200; $i++) {
            $quote = $quotes[array_rand($quotes)]; // Sélection d'un devis aléatoire
            $product = $products[array_rand($products)]; // Sélection d'un produit aléatoire

            $quoteLine = new QuoteLine();
            $quoteLine->setQuote($quote)
                ->setProductName(substr($product->getName(), 0, 255))
                ->setProductDescription(substr($product->getDescription(), 0, 255))
                ->setUnitPrice($faker->randomFloat(2, 10, 500))
                ->setQuantity($faker->numberBetween(1, 10))
                ->setDiscount($faker->randomFloat(2, 0, 50))
                ->setDescription(substr($faker->paragraph(), 0, 255));

            $manager->persist($quoteLine); // Persiste la ligne de devis
        }

        // Sauvegarde toutes les lignes de devis
        $manager->flush();
    }

    /**
     * Dépendances pour charger les fixtures dans le bon ordre
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            QuoteFixtures::class,  // Charge les devis en premier
            ProductFixtures::class, // Charge les produits en second
        ];
    }
}
