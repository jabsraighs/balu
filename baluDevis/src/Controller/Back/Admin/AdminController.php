<?php

namespace App\Controller\Back\Admin;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[isGranted("ROLE_ADMIN")]
class AdminController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(User $user,InvoiceRepository $invoiceRepository,QuoteRepository $quoteRepository, ClientRepository $clientRepository): Response {
        if($this->isGranted('ROLE_ADMIN')) {
            $invoices = $invoiceRepository->findAll();
            $quotes= $quoteRepository->findAll();
            $clients = $clientRepository->findAll();
        return $this->render('Back/admin/index.html.twig', [
            'quotes' => $quotes,
            'invoices' => $invoices,
            'clients' => $clients
        ]);
        }
    }
}
