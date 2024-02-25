<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void {

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

        for($i = 0 ; $i < count($categories) ; $i++) {
            $object= (new Category())
                ->setName($categories[array_rand($categories)]);
                $manager->persist($object);
        }
        $manager->flush();
    }

}


