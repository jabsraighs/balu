<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $products = [
            ["Site Web Vitrine", "Création d'un site web professionnel responsive avec présentation de l'entreprise, services et formulaire de contact.", 1500],
            ["Site E-commerce", "Plateforme de vente en ligne complète avec paiement sécurisé, gestion de stock et espace client.", 3800],
            ["Application Web", "Application web sur mesure avec interface d'administration et fonctionnalités personnalisées.", 4500],
            ["Application Mobile iOS", "Développement d'application native pour iPhone et iPad avec publication sur l'App Store.", 5800],
            ["Application Mobile Android", "Développement d'application native pour smartphones et tablettes Android avec publication sur le Play Store.", 5500],
            ["Application Mobile Cross-platform", "Application mobile développée avec React Native ou Flutter, fonctionnant sur iOS et Android.", 7200],
            ["Maintenance Web Mensuelle", "Service de maintenance, mises à jour et sécurité pour votre site web, forfait mensuel.", 250],
            ["Audits de Performance", "Analyse complète des performances de votre site ou application avec recommandations d'optimisation.", 980],
            ["SEO et Référencement", "Optimisation pour les moteurs de recherche, analyse des mots-clés, amélioration du positionnement.", 890],
            ["Hébergement Cloud Premium", "Solution d'hébergement haute disponibilité avec sauvegarde quotidienne et support 24/7.", 150],
            ["Formation WordPress", "Session de formation à l'utilisation et l'administration de votre site WordPress.", 690],
            ["Migration de Site Web", "Transfert complet de votre site web vers un nouvel hébergement sans perte de données.", 1200],
            ["Intégration CRM", "Mise en place et configuration d'un CRM adapté à vos besoins commerciaux.", 2400],
            ["Base de données sur mesure", "Conception et développement d'une base de données optimisée pour votre activité.", 3200],
            ["Système de réservation en ligne", "Module de prise de rendez-vous ou réservation intégré à votre site web.", 1800]
        ];
        
        $mainCompany = $this->getReference('main-company', \App\Entity\Company::class);
        
        foreach ($products as $index => $productData) {
            $product = new Product();
            $product->setName($productData[0])
                  ->setDescription($productData[1])
                  ->setUnitPrice($productData[2])
                  ->setCategory($this->getReference('category-' . ($index % 10), \App\Entity\Category::class)) // Associer à une catégorie
                  ->setCompany($mainCompany);
            
            $manager->persist($product);
            $this->addReference('product-' . $index, $product);
        }
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            CompanyFixtures::class,
        ];
    }
}
