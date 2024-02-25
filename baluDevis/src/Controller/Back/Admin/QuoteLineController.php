<?php

namespace App\Controller\Back\Admin;

use App\Entity\QuoteLine;
use App\Form\QuoteLineType;
use App\Repository\QuoteLineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[isGranted("ROLE_ADMIN","")]
#[Route('/user/quote/line',name: '_user')]
class QuoteLineController extends AbstractController
{
    #[Route('/', name: '_quote_line_index', methods: ['GET'])]
    public function index(QuoteLineRepository $quoteLineRepository): Response
    {
        return $this->render('Back/admin/user/quote_line/index.html.twig', [
            'quote_lines' => $quoteLineRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_quote_line_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quoteLine = new QuoteLine();
        $form = $this->createForm(QuoteLineType::class, $quoteLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quoteLine);
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_quote_line_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/admin/user/quote_line/new.html.twig', [
            'quote_line' => $quoteLine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_quote_line_show', methods: ['GET'])]
    public function show(QuoteLine $quoteLine): Response
    {
        return $this->render('Back/admin/user/quote_line/show.html.twig', [
            'quote_line' => $quoteLine,
        ]);
    }

    #[Route('/{id}/edit', name: '_quote_line_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QuoteLine $quoteLine, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuoteLineType::class, $quoteLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_quote_line_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/admin/user/quote_line/edit.html.twig', [
            'quote_line' => $quoteLine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_quote_line_delete', methods: ['POST'])]
    public function delete(Request $request, QuoteLine $quoteLine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quoteLine->getId(), $request->request->get('_token'))) {
            $entityManager->remove($quoteLine);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_admin_user_quote_line_index', [], Response::HTTP_SEE_OTHER);
    }
}
