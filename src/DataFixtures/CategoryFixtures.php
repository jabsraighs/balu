<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Récupérer une entreprise existante (vous pouvez adapter cela selon vos besoins)
        $company = $manager->getRepository(Company::class)->find(1); // Par exemple, prendre l'entreprise avec ID = 1

        // Si l'entreprise n'existe pas, vous pouvez en créer une nouvelle (exemple)
        if (!$company) {
            $company = (new Company())->setName('Ma société');
            $manager->persist($company);
            $manager->flush(); // Nécessaire si on crée une nouvelle entreprise
        }

        $categories = [
            "Consultation",
            "Conception",
            "Rédaction et Traduction",
            "Formation et Enseignement",
            "Développement et Programmation",
            "Marketing et Publicité",
            "Comptabilité et Finance",
            "Consultation en Ressources Humaines",
            "Photographie et Vidéo",
            "Formation et Coaching"
        ];

        foreach ($categories as $categoryName) {
            $category = (new Category())
                ->setName($categoryName)
                ->setCompany($company);  // Associer la catégorie à l'entreprise

            $manager->persist($category);
        }

        $manager->flush();
    }
}
