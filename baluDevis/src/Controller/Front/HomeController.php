<?php

namespace App\Controller\Front;

use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


#[IsGranted("ROLE_USER")]
class HomeController extends AbstractController
{
    #[Route('/accueil', name: '_accueil',methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function getAccueil(UserRepository $userRepository,InvoiceRepository $invoiceRepository,QuoteRepository $quoteRepository , ClientRepository $clientRepository): Response
    {
        $invoices = $invoiceRepository->findAll();
        $quotes= $quoteRepository->findAll();
        $clients = $clientRepository->findAll();
        return $this->render('Front/home/index.html.twig', [
            'quotes' => $quotes,
            'invoices' => $invoices,
            'clients' => $clients
        ]);
    }
}
