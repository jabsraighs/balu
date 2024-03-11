<?php

namespace App\Controller\Front;

use App\Entity\Company;
use App\Entity\UserInformation;
use App\Form\CompanyType;
use App\Form\UserInformationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted("ROLE_USER")]
class HomeController extends AbstractController
{
    #[Route('/accueil', name: '_accueil', methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function getAccueil(UserRepository $userRepository): Response
    {
        return $this->render('Front/home/index.html.twig', []);
    }

    #[Route('/onboarding', name: '_onboarding', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function getOnboarding(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $userInformation = $this->getUser()->getUserInformation() ?? new UserInformation();

        if(!empty($userInformation)) {
            return $this->redirectToRoute('front_accueil', [], Response::HTTP_SEE_OTHER);
        }
        
        $userInformation->setEmail($this->getUser()->getUserIdentifier());
        $form = $this->createForm(UserInformationType::class, $userInformation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userInformation->setUser($this->getUser());
            $entityManager->persist($userInformation);
            $entityManager->flush();
            // Persist and flush the entities
            return $this->redirectToRoute('front_accueil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/home/onboarding.html.twig', [
            'form' => $form,
        ]);
    }
}
