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
    #[Route('/', name: 'home',methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function getHome(UserRepository $userRepository): Response
    {
        $user = $this->getUser()->getId();
        $userInfo = $userRepository->findBy(['id' => $user]);

        return $this->render('Front/home/index.html.twig', [
            'user' => $userInfo,
        ]);
    }
}
