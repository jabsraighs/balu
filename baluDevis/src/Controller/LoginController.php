<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use App\Entity\User;
use App\Form\EmailFormType;
use App\Service\SendEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
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
    #[Route('/', name: 'app_forget_password', methods: ['GET', 'POST'])]
    public function register(Request $request,SendEmailService $mailService): Response
    {

        $user = new User();
        $form = $this->createForm(EmailFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password

            // generate a signed url and email it to the user
            $mailService->send(
                    'app_verify_email',
                    $user,
                    'devisbalu698@gmail.com',
                    $user->getEmail(),
                    'Please Confirm your Email to verify it. ',
                    'registration/confirmation_email.html.twig',

            );
            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/verifEmail.html.twig', [
            'login' => $form->createView(),
        ]);
    }
}