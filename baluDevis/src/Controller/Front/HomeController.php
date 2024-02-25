<?php

namespace App\Controller\Front;

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
    public function getAccueil(UserRepository $userRepository): Response
    {
        return $this->render('Front/home/index.html.twig', [
        ]);
    }
}
