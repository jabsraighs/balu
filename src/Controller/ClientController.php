<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
#[IsGranted('ROLE_COMPANY')]
final class ClientController extends AbstractController
{
    #[Route(name: 'app_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        $company = $this->getUser()->getCompany();

        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findBy(['company' => $company]),
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $client->setCompany($this->getUser()->getCompany());
        $client->setCreatedAt(new \DateTimeImmutable());
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        if ($client->getCompany() !== $this->getUser()->getCompany()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à ce client.');
            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($client->getCompany() !== $this->getUser()->getCompany()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à ce client.');
            return $this->redirectToRoute('app_client_index');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager, QuoteRepository $quoteRepository, InvoiceRepository $invoiceRepository): Response
    {
        if ($client->getCompany() !== $this->getUser()->getCompany()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à ce client.');
            return $this->redirectToRoute('app_client_index');
        }

        $countQuotes = $quoteRepository->count(['client' => $client]);
        $countInvoices = $invoiceRepository->count(['client' => $client]);

        if ($countQuotes > 0 || $countInvoices > 0) {
            $this->addFlash(
                'error',
                'Impossible de supprimer ce client : il a ' .
                ($countQuotes ? "{$countQuotes} devis" : '') .
                ($countQuotes && $countInvoices ? ' et ' : '') .
                ($countInvoices ? "{$countInvoices} factures" : '') .
                ' associés.'
            );
            return $this->redirectToRoute('app_client_index');
        }

        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
