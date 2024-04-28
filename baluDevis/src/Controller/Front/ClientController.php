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
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/user/client',name: '_user')]
#[isGranted("ROLE_USER")]
class ClientController extends AbstractController
{
    #[Route('/', name: '_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        
        $user = $this->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_COMPTABLE', $roles)) {
            $entreprise = $user->getEntreprise();
            if ($entreprise === null) {
                return $this->render('bundles\twigBundles\Exception\errorPartenaire.html.twig', [
                   'message' => 'An error occurred: Enterprise already exists. Please try again later or contact support.'
               ]);
           }
            $userClients = $entreprise->getEntrepriseClients();
        }
        elseif (in_array('ROLE_USER_ENTREPRISE', $roles)) {
            $entreprise = $user->getEntreprise();
            if ($entreprise === null) {
                return $this->render('bundles\twigBundles\Exception\errorPartenaire.html.twig', [
                   'message' => 'An error occurred: Enterprise already exists. Please try again later or contact support.'
               ]);
           }
            $userClients = $entreprise->getEntrepriseClients();
        }
        else {
            $userClients = $clientRepository->findBy(['userClient' => $user->getId()]);            
        }
        return $this->render('Front/user/client/index.html.twig', [
            'clients' => $userClients,
        ]);
    }

    #[Route('/new', name: '_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $user = $this->getUser();
        $entreprise = $user->getEntreprise();
        if (in_array('ROLE_COMPTABLE', $user->getRoles()) and $entreprise === null) {
             return $this->render('bundles\twigBundles\Exception\errorPartenaire.html.twig', [
                'message' => 'An error occurred: Enterprise already exists. Please try again later or contact support.'
            ]);
        }
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array('ROLE_COMPTABLE', $user->getRoles())) {
                $client->setEntreprise($user->getEntreprise());
                $entreprise->addEntrepriseClient($client);
                $entityManager->persist($client);
                $entityManager->flush();
                return $this->redirectToRoute('front_user_client_index', [], Response::HTTP_SEE_OTHER);
    
            } elseif (in_array('ROLE_AUTO_ENTREPRENEUR', $user->getRoles()))  {
                $client->setUserClient($user);
                $entityManager->persist($client);
                $entityManager->flush();
                return $this->redirectToRoute('front_user_client_index', [], Response::HTTP_SEE_OTHER);
            }
            else {
                // If user does not have access, return an error message
                $this->addFlash('error', 'You do not have access to create a client.');
            }

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

