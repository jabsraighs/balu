<?php

namespace App\Controller\Front;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/client',name: '_user')]
class ClientController extends AbstractController
{
    #[Route('/', name: '_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        $user = $this->getUser()->getId();
        $userClients = $clientRepository->findBy(['userClient' => $user]);

        return $this->render('Front/user/client/index.html.twig', [
            'clients' => $userClients,
        ]);
    }



    #[Route('/new', name: '_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $user = $this->getUser();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = $client->setUserClient($user);
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('front_user_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('Front/user/client/show.html.twig', [
            'client' => $client,
            dump($client)
        ]);
    }


    #[Route('/{id}/edit', name: '_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('front_user_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: '_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_user_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
