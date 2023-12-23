<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[route('/')]
class HomeController extends AbstractController
{
    #[Route('/', name: '_home',methods: ['GET'])]
    public function getHome(): Response
    {
        return $this->render('Front/home/index.html.twig', []);
    }
    #[Route('/accueil', name: '_accueil',methods: ['GET'])]
    public function getAccueil(): Response
    {
        return $this->render('Front/home/accueil.html.twig', []);
    }
}
