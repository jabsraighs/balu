<?php

namespace App\Controller\Back\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[isGranted('ROLE_ADMIN')]
#[Route('/product')]
class ProductController extends AbstractController
{

    #[Route('/', name: '_user_product_index', methods: ['GET'])]
    public function accueil(ProductRepository $productRepository): Response
    {
        return $this->render('Back/admin/product/index.html.twig', [
            'products' => $productRepository->findAll(),
            dump()
        ]);
    }

    #[Route('/new', name: '_user_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $connectedUser = $this->getUser();
            $product->setUser($connectedUser);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/admin/user/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_user_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('Back/admin/user/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: '_user_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $connectedUser = $this->getUser();
            $product->setUser($connectedUser);
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/admin/user/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_user_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_admin_user_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
