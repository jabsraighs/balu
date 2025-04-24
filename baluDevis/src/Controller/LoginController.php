<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use App\Entity\User;
// use App\Form\EmailFormType;
// use App\Form\ForgetPasswordFormType;
// use App\Repository\UserRepository;
// use App\Service\SendEmailService;
// use App\Service\VerifEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
// use Symfony\Contracts\Translation\TranslatorInterface;
// use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

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
    // #[Route('/forget/password', name: 'app_forget_password', methods: ['GET', 'POST'])]
    // public function forgetPassword(Request $request,SendEmailService $mailService,UserRepository $userRepository): Response
    // {

    //     $form = $this->createForm(EmailFormType::class);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $user = $userRepository->findOneByEmail($form->get('email')->getData());
    //         if($user) {
    //            // generate a signed url and email it to the user
    //             $mailService->send(
    //                     'app_verify_email_reset_password',
    //                     $user,
    //                     'devisbalu698@gmail.com',
    //                     $user->getEmail(),
    //                     'Please Confirm your Email to verify it for renew your password. ',
    //                     'ResetPassword/confirmation_email.html.twig',

    //             );
    //         }

    //             $this->addFlash('danger','un problème est survenue');

    //         return $this->redirectToRoute('app_login');
    //     }

    //     return $this->render('ResetPassword/verifEmail.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }

    // #[Route('/verify/email/reset/password', name: 'app_verify_email_reset_password', methods: ['GET'])]
    // public function verifyUserEmailResetPassword(VerifEmailService $verifEmailService, Request $request, TranslatorInterface $translator): Response
    // {

    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    //     $this->addFlash('success', 'Email verified successfully!');
    //     return $this->redirectToRoute('app_reset_forget_password');
    // }
    // #[Route('/reset/password/', name: 'app_reset_forget_password', methods: ['GET', 'POST'])]
    // public function ResetPassword(Request $request,UserRepository $userRepository): Response
    // {
        
    //     $form = $this->createForm(ForgetPasswordFormType::class);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $user = $userRepository->findOneByEmail($form->get('email')->getData());


    //             $this->addFlash('danger','flashMessage');

    //         // do anything else you need here, like send an email
    //         return $this->redirectToRoute('app_login');
    //     }

    //     return $this->render('ResetPassword/resetPassword.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }
}