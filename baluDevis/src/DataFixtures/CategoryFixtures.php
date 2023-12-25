<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void {

        $categories = ['vitrine','e-commerce','application mobile','application web','site communautaire','blog'];

        for($i = 0 ; $i < count($categories) ; $i++) {
            $object= (new Category())
                ->setName($categories[array_rand($categories)]);
                $manager->persist($object);
        }
        $manager->flush();
    }

}


