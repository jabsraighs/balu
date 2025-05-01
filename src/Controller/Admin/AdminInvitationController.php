<?php

namespace App\Controller\Admin;

use App\Entity\Invitation;
use App\Form\InvitationType;
use App\Repository\InvitationRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/invitation')]
#[IsGranted('ROLE_ADMIN')]
class AdminInvitationController extends AbstractController
{
    #[Route('/', name: 'admin_invitation_index', methods: ['GET'])]
    public function index(InvitationRepository $invitationRepository): Response
    {
        return $this->render('admin/invitation/index.html.twig', [
            'invitations' => $invitationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_invitation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        $invitation = new Invitation();
        $invitation->setCreatedAt(new \DateTimeImmutable());
        $invitation->setExpiresAt((new \DateTimeImmutable())->modify('+7 days'));
        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invitation->setToken(bin2hex(random_bytes(32)));
            $invitation->setInvitedBy($this->getUser());
            
            $entityManager->persist($invitation);
            $entityManager->flush();
            
            $emailService->sendInvitationEmail($invitation);

            $this->addFlash('success', 'Invitation envoyée à ' . $invitation->getEmail());
            return $this->redirectToRoute('admin_invitation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/invitation/new.html.twig', [
            'invitation' => $invitation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_invitation_show', methods: ['GET'])]
    public function show(Invitation $invitation): Response
    {
        return $this->render('admin/invitation/show.html.twig', [
            'invitation' => $invitation,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_invitation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invitation $invitation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_invitation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/invitation/edit.html.twig', [
            'invitation' => $invitation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_invitation_delete', methods: ['POST'])]
    public function delete(Request $request, Invitation $invitation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invitation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($invitation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_invitation_index', [], Response::HTTP_SEE_OTHER);
    }
}
