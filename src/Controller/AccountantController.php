<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountantController extends AbstractController{
    #[Route('/accountant', name: 'app_accountant')]
    public function index(): Response
    {
        return $this->render('accountant/index.html.twig', [
            'controller_name' => 'AccountantController',
        ]);
    }
}
