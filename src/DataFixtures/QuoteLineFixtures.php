<?php

namespace App\DataFixtures;

use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class QuoteLineFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupération de tous les devis
        $quotes = $manager->getRepository(Quote::class)->findAll();
        
        foreach ($quotes as $index => $quote) {
            // Nombre de lignes par devis (entre 1 et 4)
            $lineCount = rand(1, 4);
            $totalAmount = 0;
            
            // Produits déjà utilisés dans ce devis
            $usedProducts = [];
            
            for ($i = 0; $i < $lineCount; $i++) {
                // Sélection d'un produit non encore utilisé dans ce devis
                do {
                    $productIndex = rand(0, 14);
                    $productRef = 'product-' . $productIndex;
                } while (in_array($productIndex, $usedProducts) && count($usedProducts) < 15);
                
                $usedProducts[] = $productIndex;
                $product = $this->getReference($productRef, Product::class);
                
                // Création de la ligne de devis
                $quoteLine = new QuoteLine();
                $quantity = rand(1, 3);
                $discount = rand(0, 15); // Pourcentage de remise
                
                $quoteLine->setQuote($quote)
                    ->setProductName($product->getName())
                    ->setProductDescription($product->getDescription())
                    ->setUnitPrice($product->getUnitPrice())
                    ->setQuantity($quantity)
                    ->setDiscount($discount)
                    ->setDescription("Prestation de " . $product->getName());
                
                // Calcul du montant total de la ligne
                $lineAmount = $product->getUnitPrice() * $quantity * (1 - $discount/100);
                $totalAmount += $lineAmount;
                
                $manager->persist($quoteLine);
            }
            
            // Mise à jour du montant total du devis
            $quote->setTotalAmount($totalAmount);
            $manager->persist($quote);
        }
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            QuoteFixtures::class,
            ProductFixtures::class,
        ];
    }
}
