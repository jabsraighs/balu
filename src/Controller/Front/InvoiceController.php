<?php

namespace App\Controller\Front;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use App\Service\CalculService;
use App\Service\DomPdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user/invoice',name: '_user')]
#[isGranted("ROLE_USER")]
class InvoiceController extends AbstractController
{
    #[Route('/', name: '_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {

        $user = $this->getUser();
        $userInvoice = $invoiceRepository->findBy(
            ['userInvoice' => $user],
            ['id' => 'Desc']
            );

        return $this->render('Front/user/invoice/index.html.twig', [
            'invoices' => $userInvoice,
        ]);
    }

    #[Route('/new', name: '_invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager , ClientRepository $clientRepository,CalculService $calculService): Response
    {
        $user = $this->getUser();
        $invoice = new Invoice();
        $clientId = $request->query->get('client_id');
        if($clientId !== null) {
            $client = $clientRepository->find($clientId);
            $invoice->setClient($client);
        }
        $clientsInvoice = $clientRepository->findBy(['userClient' => $user]);
        // Créer le formulaire et transmettre les clients
        $form = $this->createForm(InvoiceType::class,$invoice, ['client' => $clientsInvoice]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $calculService->calculInvoice($invoice,$user);
                $entityManager->persist($invoice);
                $invoiceName = $invoice->generateInvoiceName();
                $invoice = $invoice->setName($invoiceName);
                $entityManager->flush();
                // Persist and flush the entities
            return $this->redirectToRoute('front_user_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        $client = $invoice->getClient()->getEmail();
        return $this->render('Front/user/invoice/show.html.twig', [
            'invoice' => $invoice,
            'client' => $client
        ]);
    }

    #[Route('/{id}/edit', name: '_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager,ClientRepository $clientRepository,CalculService $calculService): Response
    {
        $user = $this->getUser();
        $clientsInvoice = $clientRepository->findBy(['userClient' => $user]);
        // Créer le formulaire et transmettre les clients
        $form = $this->createForm(InvoiceType::class,$invoice, ['client' => $clientsInvoice]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $calculService->calculInvoice($invoice,$user);
                $entityManager->persist($invoice);
                $entityManager->flush();
                // Persist and flush the entities
            return $this->redirectToRoute('front_user_invoice_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('Front/user/invoice/edit.html.twig', [
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

        return $this->redirectToRoute('front_user_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
      #[Route('/generate/pdf/{id}', name: '_invoice_generate_pdf')]
    public function generatePdf(DomPdfService $dompdfService,Invoice $invoice): Response
    {
        $client = $invoice->getClient()->getEmail();
        $htmlContent = $this->renderView('Front/user/invoice/generatePdf.html.twig', [
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
