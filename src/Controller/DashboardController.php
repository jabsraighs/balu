<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\PaymentRepository;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(InvoiceRepository $invoiceRepository, QuoteRepository $quoteRepository, ClientRepository $clientRepository, PaymentRepository $paymentRepository): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $company = $user->getCompany();

        $recentPayments = $paymentRepository->createQueryBuilder('p')
            ->join('p.invoice', 'i')
            ->where('i.company = :company')
            ->setParameter('company', $company)
            ->orderBy('p.datePaid', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            return $this->render('dashboard/admin.html.twig', [
                'user' => $user,
            ]);
        }

        if (in_array('ROLE_ACCOUNTANT', $roles, true)) {
            $revenueData = $invoiceRepository->getMonthlyRevenue($company, 6);
            $statusDist = $invoiceRepository->getStatusDistribution($company);
            $paymentData = $paymentRepository->getMonthlyPayments($company, 6);

            return $this->render('dashboard/accountant.html.twig', [
                'user' => $user,
                'revenueData' => $revenueData,
                'statusDist' => $statusDist,
                'paymentData' => $paymentData,
            ]);
        }

        $monthlyRevenueData = $invoiceRepository->getMonthlyRevenue($company);
        $monthlyRevenue = [];

        foreach ($monthlyRevenueData as $label => $amount) {
            $monthlyRevenue[] = [
                'label' => $label,
                'amount' => $amount
            ];
        }

        $maxMonthlyRevenue = $invoiceRepository->getMaxMonthlyRevenue();

        return $this->render('dashboard/index.html.twig', [
            'invoiceStats' => [
                'totalAmount' => $invoiceRepository->getTotalAmount(),
                'pendingCount' => $invoiceRepository->getPendingCount(),
                'pendingAmount' => $invoiceRepository->getPendingAmount(),
                'percentIncrease' => $invoiceRepository->getMonthlyIncreasePercentage(),
                'monthlyRevenue' => $monthlyRevenue,
                'maxMonthlyRevenue' => $maxMonthlyRevenue,
                'overdueAmount' => $invoiceRepository->getOverdueAmount(),
                'paidThisMonth' => $paymentRepository->sumPaymentsByPeriod(
                    $company,
                    new \DateTime('first day of this month'),
                    new \DateTime('last day of this month')
                )
            ],
            'recentPayments' => $recentPayments,
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
