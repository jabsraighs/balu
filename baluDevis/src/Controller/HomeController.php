<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[route('/')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home',methods: ['GET'])]
    public function getHome(): Response
    {
        return $this->render('home/index.html.twig', []);
    }
    #[Route('/accueil', name: 'app_accueil',methods: ['GET'])]
    public function getAccueil(): Response
    {
        return $this->render('home/accueil.html.twig', []);
    }
}
