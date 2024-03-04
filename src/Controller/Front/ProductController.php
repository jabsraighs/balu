<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: '_user_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $products = $productRepository->findBy(['user' => $user]);
        return $this->render('Front/user/product/index.html.twig', [
            'products' => $products,
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

            return $this->redirectToRoute('front_user_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_user_product_show', methods: ['GET'])]
    public function show(Product $product, Security $security): Response
    {
        $user = $this->getUser();

        
        if ($product->getUser()->getId() !== $user->getId()) {
            return $this->redirectToRoute('front_user_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/product/show.html.twig', [
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

            return $this->redirectToRoute('front_user_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/product/edit.html.twig', [
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

        return $this->redirectToRoute('front_user_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
