<?php

namespace App\Controller\Back\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
#[isGranted("ROLE_ADMIN")]
class AdminController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(User $user): Response {
        if($this->isGranted('ROLE_ADMIN')) {
            return $this->render('Back/admin/index.html.twig', [
                'controller_name' => 'AdminController',
            ]);
        }
    }
}
