<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


#[route('/')]

class HomeController extends AbstractController
{
    #[Route('/', name: '_home',methods: ['GET'])]

    public function getHome(): Response
    {
        return $this->render('Front/user/home/index.html.twig', []);
    }

    #[Route('/accueil', name: '_accueil',methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function getAccueil(): Response
    {
        return $this->render('Front/user/home/accueil.html.twig', []);
    }
}
