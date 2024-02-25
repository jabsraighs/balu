<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/', name: 'app_login',methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(EntityManager $entityManager,User $user): void{
         $user = $this->getUser();

         if ($this->isGranted('ROLE_ADMIN')) {
            // Modify isVerified based on the user's role.

            // Modify the isVerified property based on your conditions.
            $this->$user->setIsVerified(false); // Replace with your actual logic.

            // Save the changes to the user entity.
            $entityManager->persist($user);
            $entityManager->flush();
        }
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

    }
}