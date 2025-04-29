<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Form\InvitationType;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/company/invitation')]
#[IsGranted('ROLE_COMPANY')]
final class InvitationController extends AbstractController
{
    #[Route('/new', name: 'app_invitation_new', methods: ['GET', 'POST'])]
    public function new(
        Request $req,
        EntityManagerInterface $em,
        EmailService $emails
    ) {
        $inv = new Invitation();
        $form = $this->createForm(InvitationType::class, $inv);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('roles')->getData();
            $inv->setToken(bin2hex(random_bytes(32)))
                ->setCompany($this->getUser()->getCompany())
                ->setInvitedBy($this->getUser())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setRoles([$role])
                ->setExpiresAt((new \DateTimeImmutable())->modify('+7 days'));

            $em->persist($inv);
            $em->flush();

            $emails->sendInvitationEmail($inv);

            $this->addFlash('success', 'Invitation envoyée à ' . $inv->getEmail());
            return $this->redirectToRoute('app_invitation_list');
        }

        return $this->render('invitation/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/', name: 'app_invitation_list')]
    public function list(EntityManagerInterface $em)
    {
        $invitations = $em->getRepository(Invitation::class)
            ->findBy(['company' => $this->getUser()->getCompany()]);
        return $this->render('invitation/list.html.twig', [
            'invitations' => $invitations
        ]);
    }
}
