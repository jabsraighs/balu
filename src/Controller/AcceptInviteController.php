<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\User;
use App\Form\AcceptInviteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/accept-invite')]
class AcceptInviteController extends AbstractController
{
    #[Route('/{token}', name: 'accept_invite', methods: ['GET', 'POST'])]
    public function accept(
        string $token,
        Request $req,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ) {
        $inv = $em->getRepository(Invitation::class)
            ->findOneBy(['token' => $token, 'usedAt' => null]);
        if (!$inv || $inv->getExpiresAt() < new \DateTime()) {
            throw $this->createNotFoundException('Invitation invalide ou expirée.');
        }

        $user = new User();
        $form = $this->createForm(AcceptInviteType::class, $user, [
            'email_readonly' => $inv->getEmail()
        ]);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $hasher->hashPassword($user, $form->get('plainPassword')->getData())
            );
            $user->setEmail($inv->getEmail());
            $user->setRoles($inv->getRoles());
            $user->setCompany($inv->getCompany());
            $em->persist($user);

            $inv->setUsedAt(new \DateTimeImmutable());
            $em->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('accept_invite/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
