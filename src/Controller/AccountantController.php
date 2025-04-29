<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ACCOUNTANT')]
final class AccountantController extends AbstractController{
    #[Route('/report', name:'accountant_report')]
    public function report()
    {
        // Affiche un form JS pour choisir période/format
        return $this->render('accountant/report.html.twig');
    }


    #[Route('/report/export.csv', name: 'accountant_export_csv')]
    public function exportCsv(InvoiceRepository $invoices): BinaryFileResponse
    {
        $company = $this->getUser()->getCompany();
        $lines = $invoices->findAllForCompany($company);

        $csv  = "N° Facture;Client;Date;Montant;Statut\n";
        foreach ($lines as $inv) {
            $csv .= sprintf(
                "%s;%s;%s;%.2f;%s\n",
                $inv->getInvoiceNumber(),
                $inv->getClient()->getName(),
                $inv->getDateCreated()->format('Y-m-d'),
                $inv->getTotalAmount(),
                $inv->getStatus()
            );
        }

        $handle = fopen('php://memory', 'r+');
        fwrite($handle, $csv);
        rewind($handle);
        
        return $this->file(
            file: $handle,
            fileName: 'report-'.date('Y-m-d').'.csv',
            disposition: 'attachment',
        );
    }
}
