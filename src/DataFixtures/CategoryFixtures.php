<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupération de l'entreprise principale
        $mainCompany = $this->getReference('main-company', \App\Entity\Company::class);
        
        // Catégories de services pertinentes pour une entreprise
        $categories = [
            "Consultation informatique",
            "Développement logiciel",
            "Développement web",
            "Applications mobiles",
            "Infrastructures cloud",
            "Formation professionnelle",
            "Support technique",
            "Analyse de données",
            "Cybersécurité",
            "Marketing digital"
        ];

        foreach ($categories as $index => $categoryName) {
            $category = new Category();
            $category->setName($categoryName)
                   ->setCompany($mainCompany);
            
            $manager->persist($category);
            $this->addReference('category-' . $index, $category);
        }

        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
        ];
    }
}
