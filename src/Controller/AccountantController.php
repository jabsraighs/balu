<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use App\Service\PdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/accountant')]
#[IsGranted('ROLE_ACCOUNTANT')]
class AccountantController extends AbstractController
{
    #[Route('/report', name: 'accountant_report')]
    public function report(
        Request $req,
        InvoiceRepository $invoiceRepository,
        QuoteRepository $quoteRepository
    ): Response {
        $reportData = $invoiceRepository->getFinancialReportData();
        
        $reportData['totalPaid'] = $reportData['totalPaid'] ?? 0;
        $reportData['totalUnpaid'] = $reportData['totalUnpaid'] ?? 0;
        $reportData['averageInvoice'] = $reportData['averageInvoice'] ?? 0;
        $reportData['topClients'] = $reportData['topClients'] ?? 0;
        $reportData['paymentMethods'] = $reportData['paymentMethods'] ?? 0;
        
        return $this->render('accountant/report.html.twig', [
            'reportData' => $reportData,
            'conversionRate' => $quoteRepository->getConversionRate()
        ]);
    }
    
    #[Route('/report/export', name: 'app_financial_report_export')]
    public function exportReport(
        InvoiceRepository $invoiceRepository,
        QuoteRepository $quoteRepository,
        PdfService $pdfService
    ): Response {
        $reportData = $invoiceRepository->getFinancialReportData();
        
        $reportData['totalPaid'] = $reportData['totalPaid'] ?? 0;
        $reportData['totalUnpaid'] = $reportData['totalUnpaid'] ?? 0;
        $reportData['averageInvoice'] = $reportData['averageInvoice'] ?? 0;
        $reportData['topClients'] = $reportData['topClients'] ?? [];
        $reportData['paymentMethods'] = $reportData['paymentMethods'] ?? [];
        
        $conversionRate = $quoteRepository->getConversionRate();
        
        $html = $this->renderView('accountant/report_pdf.html.twig', [
            'reportData' => $reportData,
            'conversionRate' => $conversionRate
        ]);
        
        $pdfContent = $pdfService->generatePdf($html, [
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true
        ]);
        
        $response = new Response($pdfContent);
        
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="rapport-financier-' . date('Y-m-d') . '.pdf"');
        
        return $response;
    }
}