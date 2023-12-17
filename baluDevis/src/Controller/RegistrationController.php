<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Login\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\SendEmailService;
use App\Service\VerifEmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/register')]
class RegistrationController extends AbstractController{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,SendEmailService $mailService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('message', 'Your account has been created Please Confirm your Email to verifie it..');
            // generate a signed url and email it to the user
            $mailService->send(
                    'app_verify_email',
                    $user,
                    'devisbalu698@gmail.com',
                    $user->getEmail(),
                    'Please Confirm your Email to verifie it. ',
                    'registration/confirmation_email.html.twig',

            );
            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

   #[Route('/verify/email/', name: 'app_verify_email', methods: ['GET'])]
public function verifyUserEmail(VerifEmailService $verifEmailService, Request $request, TranslatorInterface $translator): Response
{
    $user = $this->getUser();
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    // validate email confirmation link, sets User::isVerified=true and persists
    try {
        $verifEmailService->handleEmailConfirmation($request, $user);
    } catch (VerifyEmailExceptionInterface $exception) {
        $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

        return $this->redirectToRoute('app_accueil');
    }

    // Redirect or do something else after successful verification
    // For example, you can add a flash message and redirect to a success page.
    $this->addFlash('success', 'Email verified successfully!');
    return $this->redirectToRoute('app_accueil');
}
    #[Route('/resend/email', name: 'app_Resend_email',methods: ['GET','POST'])]
    public function reSendEmail(SendEmailService $mailService): Response {

     $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        try {
           $mailService->send(
                    'app_verify_email',
                    $user,
                    'devisbalu698@gmail.com',
                    $user->getEmail(),
                    'Please Confirm your Email to verifie it. ',
                    'registration/confirmation_email.html.twig',

            );
            $this->addFlash('success', 'Un email de vérification a été envoyé à votre adresse.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur s\'est produite lors de l\'envoi de l\'email de vérification.');
        }

        return $this->redirectToRoute('app_accueil');
    }

}
