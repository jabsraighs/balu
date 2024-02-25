<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class VerifEmailService{
    private $verifyEmailHelper;
    private $entityManager;

    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, EntityManagerInterface $entityManager)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->entityManager = $entityManager;
    }

    public function handleEmailConfirmation(Request $request,User $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        $user->setIsVerified(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
    public function resetPassword(Request $request, User $user): void
{
    // 1. Perform password reset logic here

    // 2. Redirect to the reset password page
    return redirect()->route('reset.password.page'); // Assuming 'reset.password.page' is the route name for the reset password page.
}
}