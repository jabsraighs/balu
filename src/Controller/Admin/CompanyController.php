<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path:'/admin/company')]
#[IsGranted('ROLE_ADMIN')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CompanyController extends AbstractController
{
    #[Route('/index', name: 'app_admin_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository): Response
    {
        $companies = $companyRepository->findAll();
        return $this->render('admin/company/index.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/new', name: 'app_admin_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($company);
            $entityManager->flush();

            $this->addFlash('success', 'Entreprise créée avec succès.');
            return $this->redirectToRoute('app_admin_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/company/new.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_company_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
        return $this->render('admin/company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);
        dump($company);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Entreprise modifiée avec succès.');
            return $this->redirectToRoute('app_admin_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/company/edit.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_company_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
            $this->addFlash('success', 'Entreprise supprimée avec succès.');
        }

        return $this->redirectToRoute('app_admin_company_index', [], Response::HTTP_SEE_OTHER);
    }
}