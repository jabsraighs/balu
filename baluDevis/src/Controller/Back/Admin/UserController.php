<?php

namespace App\Controller\Back\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as HasherUserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[isGranted("ROLE_ADMIN")]
#[Route('/user',name: '_user')]
class UserController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('Back/admin/user/index.html.twig', [
            'users' => $userRepository->findAllExceptAdmin(),
        ]);
    }
// pas sur que c'est necessaire  cette route
    #[isGranted("ROLE_ADMIN")]
    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request,HasherUserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[isGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('Back/admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }
    #[isGranted("ROLE_ADMIN")]
    #[Route('/{id}/clients', name: '_show_clients', methods: ['GET'])]
    public function showClientsByUserId(ClientRepository $clientRepository,$id): Response
    {

        return $this->render('Back/admin/user/showClients.html.twig', [

        'clients' => $clientRepository->findUsersByClientId($id),

        ]);
    }
    #[isGranted("ROLE_ADMIN")]
    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[isGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
