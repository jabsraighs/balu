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
        $productsName = [
            "SitesWeb",
            "Applications Web",
            "Boutiques en ligne",
            "Applications mobiles",
            "Applications de bureau",
            "Applications cross-platform",
            "Cloud Services",
            "Data Analytics",
            "Cybersecurity Tools",
            "CRM Software",
            "ERP Solutions",
            "Social Media Tools",
            "SEO Services",
            "Email Marketing Software",
            "Content Management Systems",
            "E-commerce Solutions",
            "Project Management Tools",
            "Video Conferencing Software",
            "Graphic Design Tools",
            "Web Hosting Services"
        ];

        $categories = $manager->getRepository(Category::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 100; $i++) {
            $product = new Product();
            $product->setName($productsName[array_rand($productsName)]);
            $product->setDescription($faker->paragraph);
            $product->setPrice($faker->randomFloat(2, 10, 1000));
            $product->setCategory($categories[array_rand($categories)]);
            $product->setUser($users[array_rand($users)]);

            $manager->persist($product);
        }

        $manager->flush();
    }
     public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}

