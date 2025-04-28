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
#[Route('/', name: 'app_dashboard')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AdminController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(InvoiceRepository $invoiceRepository,QuoteRepository $quoteRepository, ClientRepository $clientRepository) {
        $user  = $this->getUser();
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            $invoices = $invoiceRepository->findAll();
            $quotes= $quoteRepository->findAll();
            $clients = $clientRepository->findAll();
            
            return $this->render('Back/admin/index.html.twig', [
                'user' => $user,
                'quotes' => $quotes,
                'invoices' => $invoices,
                'clients' => $clients,
                'invoiceStats' => [
                    'totalAmount' => $invoiceRepository->getTotalAmount(),
                    'pendingCount' => $invoiceRepository->getPendingCount(),
                    'pendingAmount' => $invoiceRepository->getPendingAmount(),
                    'percentIncrease' => $invoiceRepository->getMonthlyIncreasePercentage(),
                    'monthlyRevenue' => $invoiceRepository->getMonthlyRevenue(),
                    'maxMonthlyRevenue' => $invoiceRepository->getMaxMonthlyRevenue(),
                ],
                'quoteStats' => [
                    'totalCount' => $quoteRepository->getTotalCount(),
                    'conversionRate' => $quoteRepository->getConversionRate(),
                ],
                'clientStats' => [
                    'totalCount' => $clientRepository->getTotalCount(),
                    'newCount' => $clientRepository->getNewClientsCount(),
                ],
                'recentQuotes' => $quoteRepository->findRecent(5),
            ]);
            
        }

        if (in_array('ROLE_ACCOUNTANT', $roles, true)) {
            return $this->render('dashboard/accountant.html.twig', [
                'user' => $user,
            ]);
        }
        return $this->render('Back/admin/index.html.twig', [
            'invoiceStats' => [
                'totalAmount' => $invoiceRepository->getTotalAmount(),
                'pendingCount' => $invoiceRepository->getPendingCount(),
                'pendingAmount' => $invoiceRepository->getPendingAmount(),
                'percentIncrease' => $invoiceRepository->getMonthlyIncreasePercentage(),
                'monthlyRevenue' => $invoiceRepository->getMonthlyRevenue(),
                'maxMonthlyRevenue' => $invoiceRepository->getMaxMonthlyRevenue(),
            ],
            'quoteStats' => [
                'totalCount' => $quoteRepository->getTotalCount(),
                'conversionRate' => $quoteRepository->getConversionRate(),
            ],
            'clientStats' => [
                'totalCount' => $clientRepository->getTotalCount(),
                'newCount' => $clientRepository->getNewClientsCount(),
            ],
            'Invoices' => $invoiceRepository->findRecent(5),
            'recentQuotes' => $quoteRepository->findRecent(5),
            'user' => $user,
            'invoices' => $invoiceRepository->findRecent(5)
        ]);
    }
}

