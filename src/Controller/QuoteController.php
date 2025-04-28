<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Entity\Quote;
use App\Form\QuoteType;
use App\Repository\ProductRepository;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Snappy\Pdf;

#[Route('/quote')]
#[IsGranted('ROLE_COMPANY')]
final class QuoteController extends AbstractController
{
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

    #[Route('/{id}/convert-to-invoice', name: 'app_quote_convert_to_invoice', methods: ['GET'])]
    public function convertToInvoice(Quote $quote, EntityManagerInterface $entityManager): Response
    {
        if ($quote->getStatus() === 'accepted') {
            $this->addFlash('warning', 'Ce devis a déjà été accepté.');
            return $this->redirectToRoute('app_quote_show', ['id' => $quote->getId()]);
        }

        $invoice = new Invoice();
        $invoice->setCompany($quote->getCompany());
        $invoice->setClient($quote->getClient());
        $invoice->setCustomer($quote->getCustomer());
        $invoice->setStatus('pending');

        $invoice->setInvoiceNumber('INV-' . date('Ymd') . '-' . substr(uniqid(), -5));

        $dateDue = new \DateTime();
        $dateDue->modify('+30 days');
        $invoice->setDateDue($dateDue);
        $invoice->setCreatedAt(new \DateTimeImmutable());

        $totalAmount = 0;
        foreach ($quote->getQuoteLines() as $quoteLine) {
            $invoiceLine = new InvoiceLine();
            $invoiceLine->setProductName($quoteLine->getProductName());
            $invoiceLine->setProductDescription($quoteLine->getProductDescription());
            $invoiceLine->setDescription($quoteLine->getDescription());
            $invoiceLine->setQuantity($quoteLine->getQuantity());
            $invoiceLine->setUnitPrice($quoteLine->getUnitPrice());
            $invoiceLine->setDiscount($quoteLine->getDiscount());
            

            $invoice->addInvoiceLine($invoiceLine);

            $lineAmount = $quoteLine->getQuantity() * $quoteLine->getUnitPrice() * (1 - $quoteLine->getDiscount() / 100);
            $totalAmount += $lineAmount;
        }

        $invoice->setTotalAmount((string) $totalAmount);

        $quote->setStatus('accepted');

        $entityManager->persist($invoice);
        $entityManager->persist($quote);
        $entityManager->flush();

        $this->addFlash('success', 'Le devis a été converti en facture avec succès.');

        return $this->redirectToRoute('app_invoice_show', ['id' => $invoice->getId()]);
    }
    #[Route('/{id}/pdf', name: 'app_quote_pdf', methods: ['GET'])]
    public function generatePdf(Quote $quote, Pdf $knpSnappyPdf): Response
    {
        $html = $this->renderView('quote/pdf.html.twig', [
            'quote' => $quote
        ]);

        return new Response(
            $knpSnappyPdf->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="devis-' . $quote->getQuoteNumber() . '.pdf"'
            ]
        );
    }

    #[Route('/{id}/send-email', name: 'app_quote_send_email', methods: ['GET'])]
    public function sendEmail(Quote $quote, Pdf $knpSnappyPdf, MailerInterface $mailer): Response
    {
        $html = $this->renderView('quote/pdf.html.twig', [
            'quote' => $quote
        ]);
        // $pdf = $knpSnappyPdf->getOutputFromHtml($html);
        
        $email = (new Email())
            ->from('baludevis@support.com')
            ->to($quote->getClient()->getEmail())
            ->subject('Devis #' . $quote->getQuoteNumber())
            ->html($this->renderView('quote/email.html.twig', [
                'quote' => $quote,
                'app_url' => $this->getParameter('app_url'),
                'company_name' => $quote->getCompany()->getName(),
            ]));
            // ->attach($pdf, 'devis-'.$quote->getQuoteNumber().'.pdf', 'application/pdf');
        
        $mailer->send($email);
        
        $this->addFlash('success', 'Le devis a été envoyé par email avec succès.');
        
        return $this->redirectToRoute('app_quote_show', ['id' => $quote->getId()]);
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
        if ($this->isCsrfTokenValid('delete' . $quote->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quote_index', [], Response::HTTP_SEE_OTHER);
    }
}
