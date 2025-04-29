<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    public function generatePdf(string $html, array $options = []): string
    {
        $dompdf = new Dompdf();
        
        $pdfOptions = new Options();
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $pdfOptions->set('defaultFont', 'Arial');
        
        foreach ($options as $key => $value) {
            $pdfOptions->set($key, $value);
        }
        
        $dompdf->setOptions($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        return $dompdf->output();
    }
}