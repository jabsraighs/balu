<?php

namespace App\Controller\Front;

use App\Entity\Invoice;
use App\Entity\Quote;
use App\Entity\Client;
use App\Form\ClientType;
use App\Form\QuoteType;
use App\Repository\ClientRepository;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[isGranted("ROLE_USER")]
#[Route('/user/quote',name: '_user')]
class QuoteController extends AbstractController
{
    #[Route('/', name: '_quote_index', methods: ['GET'])]
    public function index(QuoteRepository $quoteRepository): Response
    {
        
        $user = $this->getUser();
       $userQuote = $quoteRepository->findBy(
    ['userQuote' => $user],
    ['id' => 'DESC'] // Order by the 'createdAt' property in descending order
);

        return $this->render('Front/user/quote/index.html.twig', [
            'quotes' => $userQuote,
        ]);
    }

    #[Route('/new', name: '_quote_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,ClientRepository $clientRepository): Response
    {
        $quote = new Quote();
        $user = $this->getUser();
        // Récupérer les clients de l'utilisateur
        $clients = $clientRepository->findBy(['userClient' => $user]);
        // Créer le formulaire et transmettre les clients

        $form = $this->createForm(QuoteType::class,$quote, ['clients' => $clients]);
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
                $quote->setUserQuote($user);
                // Persist and flush the entities
            $entityManager->persist($quote);
            $quoteName = $quote->generateName();
            $quote = $quote->setName($quoteName);
            $entityManager->flush();

            return $this->redirectToRoute('front_user_quote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/quote/new.html.twig', [
            'quote' => $quote,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_quote_show', methods: ['GET'])]
    public function show(Quote $quote,ClientRepository $clientRepository): Response
    {

        return $this->render('Front/user/quote/show.html.twig', [
            
            'quote' => $quote,

        ]);
    }

    #[Route('/{id}/edit', name: '_quote_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quote $quote, EntityManagerInterface $entityManager, ClientRepository $clientRepository): Response {
        $user = $this->getUser();
        $clients = $clientRepository->findBy(['userClient' => $user]);
        // Créer le formulaire et transmettre les clients

        $form = $this->createForm(QuoteType::class,$quote, ['clients' => $clients]);
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
                $quote->setUserQuote($user);
                // Persist and flush the entities
            $entityManager->persist($quote);

            $quote = $quote->setName($quote->getName());
            $entityManager->flush();

            return $this->redirectToRoute('front_user_quote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/quote/edit.html.twig', [
            'quote' => $quote,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/quote/invoice', name: '_quote_invoice', methods: ['GET', 'POST'])]
    public function genererFactureAuto(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response{
        //instanciation
        $invoice = new Invoice();
        $user = $this->getUser();
        $invoice = $invoice->setQuote($quote);
        //set les differents attributs de invoice
        $invoice = $invoice->setUserInvoice($user);
        $invoice = $invoice->setPaymentStatus('waiting');
        $invoice = $invoice->setTva($quote->getTva());
        $invoice = $invoice->setTotalTva($quote->getTotalTva());
        $invoice = $invoice->setTotalAmount($quote->getTotalAmount());
        $invoice = $invoice->setClient($quote->getClient());
        $entityManager->persist($invoice);
        // necessaire pour getId of facture
        $invoiceName = $invoice->generateInvoiceName();
        $invoice = $invoice->setName($invoiceName);
        $entityManager->flush();
        $this->addFlash('message', 'you create the invoice');

        return $this->redirectToRoute('front_user_invoice_index', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/{id}', name: '_quote_delete', methods: ['POST'])]
    public function delete(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quote->getId(), $request->request->get('_token'))) {
            $entityManager->remove($quote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_user_quote_index', [], Response::HTTP_SEE_OTHER);
    }
}
