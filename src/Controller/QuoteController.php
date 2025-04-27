<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Form\QuoteType;
use App\Repository\ProductRepository;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/quote')]
#[IsGranted('ROLE_COMPANY')]
final class QuoteController extends AbstractController{
    #[Route(name: 'app_quote_index', methods: ['GET'])]
    public function index(QuoteRepository $quoteRepository): Response
    {
        $user = $this->getUser();
        $company = $user->getCompany();
        
        return $this->render('quote/index.html.twig', [
            'quotes' => $quoteRepository->findByCompany($company),
        ]);
    }

    #[Route('/new', name: 'app_quote_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $company = $user->getCompany();
        
        $quote = new Quote();
        $quote->setCompany($company);
        $quote->setCustomer($user);
        $quote->setCreatedAt(new \DateTimeImmutable());
        
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($quote->getQuoteLines() as $quoteLine) {
                if (!$quoteLine->getProductName()) {
                    $quoteLine->setProductName('Produit personnalisé');
                }
                if (!$quoteLine->getProductDescription()) {
                    $quoteLine->setProductDescription('Description par défaut');
                }
            }
            
            $totalAmount = $this->calculateTotalAmount($quote);
            $quote->setTotalAmount($totalAmount);
            
            $entityManager->persist($quote);
            $entityManager->flush();

            return $this->redirectToRoute('app_quote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quote/new.html.twig', [
            'quote' => $quote,
            'form' => $form,
            'products' => $productRepository->findByCompany($company),
        ]);
    }

    #[Route('/{id}', name: 'app_quote_show', methods: ['GET'])]
    public function show(Quote $quote): Response
    {
        return $this->render('quote/show.html.twig', [
            'quote' => $quote,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quote_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quote $quote, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            foreach ($quote->getQuoteLines() as $quoteLine) {
                if (!$quoteLine->getProductName()) {
                    $quoteLine->setProductName('Produit personnalisé');
                }
                if (!$quoteLine->getProductDescription()) {
                    $quoteLine->setProductDescription('Description par défaut');
                }
            }
            
            $totalAmount = $this->calculateTotalAmount($quote);
            $quote->setTotalAmount($totalAmount);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_quote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quote/edit.html.twig', [
            'quote' => $quote,
            'form' => $form,
            'products' => $productRepository->findByCompany($quote->getCompany()),
        ]);
    }

    /**
     * Calculate the total amount of a quote based on all quote lines
     */
    private function calculateTotalAmount(Quote $quote): float
    {
        $total = 0;
        
        foreach ($quote->getQuoteLines() as $quoteLine) {
            $quantity = $quoteLine->getQuantity();
            $unitPrice = $quoteLine->getUnitPrice();
            $discount = $quoteLine->getDiscount();
            
            $lineAmount = $quantity * $unitPrice * (1 - $discount / 100);
            $total += $lineAmount;
        }
        
        return round($total, 2);
    }

    #[Route('/{id}', name: 'app_quote_delete', methods: ['POST'])]
    public function delete(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quote->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quote_index', [], Response::HTTP_SEE_OTHER);
    }
}
