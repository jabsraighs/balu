<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Entity\Quote;
use App\Entity\Client;
use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $date = new \DateTimeImmutable();

        $quotes = $manager->getRepository(Quote::class)->findAll();
        $companies = $manager->getRepository(Company::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        if (empty($quotes) || empty($companies) || empty($users)) {
            throw new \Exception("Assurez-vous d'avoir des devis, des entreprises et des utilisateurs dans la base de données.");
        }

        foreach ($quotes as $quote) {
            $invoice = (new Invoice())
                ->setInvoiceNumber($faker->unique()->numerify('INV-#######'))
                ->setStatus($faker->randomElement(['pending', 'paid', 'cancelled']))
                ->setDateDue($faker->dateTimeBetween('now', '+1 month'))
                ->setDatePaid($faker->optional()->dateTimeThisYear())
                ->setTotalAmount($quote->getTotalAmount()) // utiliser le montant du devis
                ->setCompany($companies[array_rand($companies)]) // tu peux choisir de garder la compagnie du devis aussi
                ->setClient($quote->getClient()) // récupérer le client depuis le devis
                ->setCustomer($users[array_rand($users)]) // ou peut-être lié au commercial du devis
                ->setCreatedAt($date);

            // Si ton `Quote` a des lignes, tu peux les copier aussi :
            foreach ($quote->getQuoteLines() as $quoteLine) {
                $invoiceLine = (new InvoiceLine())
                    ->setDescription($quoteLine->getDescription())
                    ->setQuantity($quoteLine->getQuantity())
                    ->setUnitPrice($quoteLine->getUnitPrice())
                    ->setProductName($quoteLine->getProductName())
                    ->setInvoice($invoice)
                    ->setProductDescription($quoteLine->getProductDescription());

                $manager->persist($invoiceLine);
            }

            $manager->persist($invoice);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
            CompanyFixtures::class,
            UserFixtures::class,
            QuoteFixtures::class,
        ];
    }
}
