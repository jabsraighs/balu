<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController{
    #[Route('/', name: 'app_dashboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(InvoiceRepository $invoiceRepository, QuoteRepository $quoteRepository, ClientRepository $clientRepository): Response
    {
        $user  = $this->getUser();
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            return $this->render('dashboard/admin.html.twig', [
                'user' => $user,
            ]);
        }

        if (in_array('ROLE_ACCOUNTANT', $roles, true)) {
            return $this->render('dashboard/accountant.html.twig', [
                'user' => $user,
            ]);
        }

        return $this->render('dashboard/index.html.twig', [
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
            'recentInvoices' => $invoiceRepository->findRecent(5),
            'recentQuotes' => $quoteRepository->findRecent(5),
            'user' => $user,
        ]);
    }
}
