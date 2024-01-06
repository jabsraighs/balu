<?php

namespace App\Controller\Front;

use App\Repository\UserRepository;
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
        return $this->render('Front/home/index.html.twig', []);
    }

    #[Route('/accueil', name: '_accueil',methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function getAccueil(UserRepository $userRepository): Response
    {
          $user = $this->getUser()->getId();
        $userInfo = $userRepository->findBy(['id' => $user]);

        return $this->render('Front/home/accueil.html.twig', [
            'users' => $userInfo,
        ]);
    }
}
