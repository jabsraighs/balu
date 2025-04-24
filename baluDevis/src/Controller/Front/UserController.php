<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as HasherUserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user',name: '_user')]
#[IsGranted('ROLE_COMPTABLE')]
class UserController extends AbstractController
{
    #[IsGranted('ROLE_COMPTABLE')]
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $entreprise = $user->getEntreprise();
        if ($entreprise === null) {
            return $this->render('bundles\twigBundles\Exception\errorPartenaire.html.twig', [
                'message' => 'An error occurred: Enterprise already exists. Please try again later or contact support.'
            ]);
        }
       
        $partenaire = $userRepository->findBy(["entreprise" => $user->getEntreprise()->getId() ]);
         return $this->render('Front/user/index.html.twig', [
             'users' => $partenaire,
         ]);
    }
    #[IsGranted('ROLE_COMPTABLE')]
    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, HasherUserPasswordHasherInterface $userPasswordHasher,SendEmailService $mailService, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $entreprise = $this->getUser()->getEntreprise();
        //verification qu'il existe une entreprise
            if ($entreprise== null) {
                return $this->render('bundles\twigBundles\Exception\errorPartenaire.html.twig', [
                    'message' => 'An error occurred: Enterprise already exists. Please try again later or contact support.'
                ]);
            }
        $partenairesEntreprise = new User();
        $partenairesEntreprise->setRoles(['ROLE_USER_ENTREPRISE']); 
        $form = $this->createForm(UserType::class, $partenairesEntreprise);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe
            $hashedPassword = $userPasswordHasher->hashPassword(
                $partenairesEntreprise,
                $form->get('plainPassword')->getData()
            );
            $entreprise->addPartenaire($partenairesEntreprise);
            $partenairesEntreprise = $partenairesEntreprise->setPassword($hashedPassword);
            // Persister l'utilisateur
            
            $entityManager->persist($entreprise);
            $entityManager->persist($partenairesEntreprise);
            $entityManager->persist($user);
            $entityManager->flush();
            // send verification email
            $mailService->send(
                'app_verify_email',
                $partenairesEntreprise,
                'devisbalu698@gmail.com',
                $partenairesEntreprise->getEmail(),
                'Please Confirm your Email to verify it. u have been add as a collaborators  ',
                'registration/confirmation_email.html.twig',

        );
            // Rediriger vers la liste des utilisateurs
            return $this->redirectToRoute('front_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/new.html.twig', [
            'user' => $partenairesEntreprise,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}', name: '_show', methods: ['GET'])]
    #[IsGranted('ROLE_COMPTABLE')]
    public function show(User $user): Response
    {
        return $this->render('Front/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/clients', name: '_show_clients', methods: ['GET'])]
    #[IsGranted('ROLE_COMPTABLE')]
    public function showClientsByUserId(ClientRepository $clientRepository,$id): Response
    {

        return $this->render('Front/user/showClients.html.twig', [

        'clients' => $clientRepository->findUsersByClientId($id),

        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_COMPTABLE')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('front_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    #[IsGranted('ROLE_COMPTABLE')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
