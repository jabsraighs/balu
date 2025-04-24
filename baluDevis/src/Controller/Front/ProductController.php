<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $roles = $user->getRoles();
        if (in_array('ROLE_COMPTABLE', $roles)) {
            $entreprise = $user->getEntreprise();
            if ($entreprise === null) {
                return $this->render('bundles\twigBundles\Exception\errorPartenaire.html.twig', [
                   'message' => 'An error occurred: Enterprise already exists. Please try again later or contact support.'
               ]);
           }
           $userProducts = $entreprise->getEntrepriseProducts();
        }
        elseif (in_array('ROLE_USER_ENTREPRISE', $roles)) {
            $entreprise = $user->getEntreprise();
            if ($entreprise === null) {
                return $this->render('bundles\twigBundles\Exception\errorPartenaire.html.twig', [
                   'message' => 'An error occurred: Enterprise already exists. Please try again later or contact support.'
               ]);
           }
            $userProducts = $entreprise->getEntrepriseProducts();
        }
        else {
            $userProducts = $productRepository->findBy(['user' => $user->getId()]);            
        }
        $products = $productRepository->findBy(['user' => $user]);
        return $this->render('Front/user/product/index.html.twig', [
            'products' => $userProducts,
        ]);
    }

    #[Route('/new', name: '_user_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $user = $this->getUser();
        if ($user->getEntreprise() === null && in_array('ROLE_COMPTABLE', $user->getRoles())) {
             return $this->render('bundles\twigBundles\Exception\errorPartenaire.html.twig', [
                'message' => 'An error occurred: Enterprise already exists. Please try again later or contact support.'
            ]);
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array('ROLE_COMPTABLE', $user->getRoles())) {
                $entreprise = $user->getEntreprise();
                $product->setEntreprise($user->getEntreprise());
                $entreprise->addEntrepriseProduct($product);
                $entityManager->persist($product);
                $entityManager->flush();
                return $this->redirectToRoute('front_user_product_index', [], Response::HTTP_SEE_OTHER);
    
            } elseif (in_array('ROLE_AUTO_ENTREPRENEUR', $user->getRoles()))  {
                $product->setUser($user);
                $entityManager->persist($product);
                $entityManager->flush();
                return $this->redirectToRoute('front_user_product_index', [], Response::HTTP_SEE_OTHER);
            }
            else {
                // If user does not have access, return an error message
                $this->addFlash('error', 'You do not have access to create a client.');
            }

            return $this->redirectToRoute('front_user_product_index', [], Response::HTTP_SEE_OTHER);
        }
       

        return $this->render('Front/user/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_user_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
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
