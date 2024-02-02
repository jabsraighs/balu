<?php

namespace App\Controller;

use App\Controller\Front\QuoteController;
use App\Entity\Quote;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DomPdfService;

class PdfController extends AbstractController
{
    #[Route('/generate/pdf/{id}', name: 'generate_pdf')]
    public function generatePdf(DomPdfService $dompdfService,Quote $quote,ClientRepository $clientRepositor): Response
    {
        $client = $quote->getClient()->getEmail();
        $htmlContent = $this->renderView('pdf/generatePdf.html.twig', [
            // Pass any necessary data to the HTML template here
            'quote' => $quote,
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