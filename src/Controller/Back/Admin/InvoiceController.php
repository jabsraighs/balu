<?php

namespace App\Controller\Back\Admin;

use App\Service\DomPdfService;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user/invoice',name: 'app_dashboard')]
#[isGranted("ROLE_ADMIN")]
class InvoiceController extends AbstractController
{
    #[Route('/', name: '_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        return $this->render('Back/admin/user/invoice/index.html.twig', [
            'invoices' => $invoiceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalAmountQuote = $invoice->getQuote()->getTotalAmount();
            $invoice = $invoice->setTotalAmount($totalAmountQuote);
            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/admin/user/invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: '_invoice_show', methods: ['GET'])]
    
    public function show(Invoice $invoice): Response
    {
        return $this->render('Back/admin/user/invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/{id}/edit', name: '_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/Admin/user/invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_admin_user_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/generate/pdf/{id}', name: '_invoice_generate_pdf')]
    public function generatePdf(DomPdfService $dompdfService,Invoice $invoice): Response
    {
        $client = $invoice->getClient()->getEmail();
        $htmlContent = $this->renderView('Back/Admin/user/invoice/generatePdf.html.twig', [
            // Pass any necessary data to the HTML template here
            'invoice' => $invoice,
            'client' => $client
        ]);

        // Generate PDF from HTML content
        $pdfContent = $dompdfService->generatePdfFromHtml($htmlContent);
        // Create a response with the PDF content
        $response = new Response($pdfContent);
        // Set headers for PDF content
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="generated.pdf"');

        return $response;
    }
}