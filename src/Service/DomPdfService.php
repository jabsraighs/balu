<?php

namespace App\Service;

use Dompdf\Dompdf;
use Twig\Environment;

class DomPdfService
{
    private $twig;
    private $dompdf;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $this->dompdf = new Dompdf();
    }

    public function generatePdfFromHtml($htmlContent)
        {
            // Toujours convertir explicitement en UTF-8
            $htmlContent = mb_convert_encoding($htmlContent, 'UTF-8', 'ASCII');

            $this->dompdf->set_option('isHtml5ParserEnabled', true);
            $this->dompdf->set_option('isUnicode', true);

            $this->dompdf->loadHtml($htmlContent);
            $this->dompdf->render();

            return $this->dompdf->output();
        }

    
}