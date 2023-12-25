<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class ProductFixtures extends Fixture implements DependentFixtureInterface {



    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr-Fr'); // Fix the namespace here
        //$date = new \DateTimeImmutable();
        $productsName = ["SitesWeb","Applications Web","Boutiques en ligne" ,"Applications mobiles","Applications de bureau","Applications cross-platform"];

        $categories = $manager->getRepository(Category::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($productsName[array_rand($productsName)]);
            $product->setDescription($faker->paragraph);
            $product->setPrice($faker->randomFloat(2, 10, 1000));
            $product->setCategory($categories[array_rand($categories)]);

            $manager->persist($product);
        }

        $manager->flush();
    }
     public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}

