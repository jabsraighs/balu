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
        $this->dompdf->loadHtml($htmlContent);

        // (Optionnel) Configurez les options de Dompdf ici

        $this->dompdf->render();

        return $this->dompdf->output();
    }
}