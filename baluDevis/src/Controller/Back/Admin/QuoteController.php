<?php

namespace App\Controller\Back\Admin;

use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Form\QuoteLineType;
use App\Form\QuoteType;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[isGranted("ROLE_ADMIN")]
#[Route('/user/quote',name: '_user')]
class QuoteController extends AbstractController
{
    #[Route('/', name: '_quote_index', methods: ['GET'])]
    public function index(QuoteRepository $quoteRepository): Response
    {
        return $this->render('Back/admin/user/quote/index.html.twig', [
            'quotes' => $quoteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_quote_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quote = new Quote();

        $form = $this->createForm(QuoteType::class, $quote);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                foreach ($quote->getQuoteLines() as $quoteLine) {
                    // Calculate subTotal for each QuoteLine (ht per item)
                    $subTotal = $quoteLine->getQuantity() * $quoteLine->getUnitPrice();
                    $quoteLine->setSubTotal($subTotal);
                }
                // Calculate totalAmount for the entire Quote
                $quoteLines = $quote->getQuoteLines() ;
                $totalTva = 0;

                foreach ($quoteLines as $quoteLine) {
                    // total tva = total ht * tva
                     $totalTva += $quoteLine->getSubTotal() * $quote->getTva();
                }
                $totalAmount = 0;

                foreach ($quoteLines as $quoteLine) {
                    $totalAmount += $quoteLine->getSubTotal() + $totalTva ;
                }

                // Set totalAmount for the Quote
                $quote->setTotalTva($totalTva);
                $quote->setTotalAmount($totalAmount);

                // Persist and flush the entities
            $entityManager->persist($quote);
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_quote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/admin/user/quote/new.html.twig', [
            'quote' => $quote,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_quote_show', methods: ['GET'])]
    public function show(Quote $quote): Response
    {
        return $this->render('Back/admin/user/quote/show.html.twig', [
            'quote' => $quote,
        ]);
    }

    #[Route('/{id}/edit', name: '_quote_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_quote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/admin/user/quote/edit.html.twig', [
            'quote' => $quote,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_quote_delete', methods: ['POST'])]
    public function delete(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quote->getId(), $request->request->get('_token'))) {
            $entityManager->remove($quote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_admin_user_quote_index', [], Response::HTTP_SEE_OTHER);
    }
}
