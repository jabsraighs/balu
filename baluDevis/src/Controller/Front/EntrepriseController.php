<?php

namespace App\Controller\Front;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user/entreprise', name:'_user')]
#[IsGranted("ROLE_COMPTABLE")]
class EntrepriseController extends AbstractController
{
    #[Route('/', name: '_entreprise_index', methods: ['GET'])]
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        $user = $this->getUser();
        $entreprises = $entrepriseRepository->findBy(['userEntreprise'=> $user->getId()]);
        return $this->render('Front/user/entreprise/index.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    #[Route('/new', name: '_entreprise_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $user = $this->getUser();
        $existAlready = $this->getUser()->getUserCreateEntreprise();
       
            if ($existAlready) {
                return $this->render('bundles\twigBundles\Exception\error.html.twig', [
                    'message' => 'An error occurred: Enterprise already exists. Please try again later or contact support.'
                ]);
            }
        
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise->setUserEntreprise($user);
            $user->setEntreprise($entreprise);
            $user->setUserCreateEntreprise($entreprise);
            $entityManager->persist($user);
            $entityManager->persist($entreprise);
            $entityManager->flush();

            return $this->redirectToRoute('front_user_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/entreprise/new.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_entreprise_show', methods: ['GET'])]
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('Front/user/entreprise/show.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

    #[Route('/{id}/edit', name: '_entreprise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('front_user_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/entreprise/edit.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_entreprise_delete', methods: ['POST'])]
    public function delete(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entreprise->getId(), $request->request->get('_token'))) {
            $entityManager->remove($entreprise);
            $entityManager->flush();
        }
        return $this->redirectToRoute('front_user_entreprise_index', [], Response::HTTP_SEE_OTHER);
    }
}
