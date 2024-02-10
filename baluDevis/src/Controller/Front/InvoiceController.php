<?php

namespace App\Controller\Front;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use App\Service\CalculService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user/invoice',name: '_user')]
#[isGranted("ROLE_USER")]
class InvoiceController extends AbstractController
{
    #[Route('/', name: '_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {

        $user = $this->getUser();
        $userInvoice = $invoiceRepository->findBy(
            ['userInvoice' => $user],
            ['id' => 'Desc']
            );

        return $this->render('Front/user/invoice/index.html.twig', [
            'invoices' => $userInvoice,
        ]);
    }

    #[Route('/new', name: '_invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager , ClientRepository $clientRepository,CalculService $calculService): Response
    {
        $user = $this->getUser();
        $invoice = new Invoice();
        $clientsInvoice = $clientRepository->findBy(['userClient' => $user]);
        // Créer le formulaire et transmettre les clients
        $form = $this->createForm(InvoiceType::class,$invoice, ['client' => $clientsInvoice]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $calculService->calculInvoice($invoice,$user);
                $entityManager->persist($invoice);
                $invoiceName = $invoice->generateInvoiceName();
                $invoice = $invoice->setName($invoiceName);
                $entityManager->flush();
                // Persist and flush the entities
            return $this->redirectToRoute('front_user_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/user/invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        $client = $invoice->getClient()->getEmail();
        return $this->render('Front/user/invoice/show.html.twig', [
            'invoice' => $invoice,
            'client' => $client
        ]);
    }

    #[Route('/{id}/edit', name: '_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager,ClientRepository $clientRepository,CalculService $calculService): Response
    {
        $user = $this->getUser();
        $clientsInvoice = $clientRepository->findBy(['userClient' => $user]);
        // Créer le formulaire et transmettre les clients
        $form = $this->createForm(InvoiceType::class,$invoice, ['client' => $clientsInvoice]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $calculService->calculInvoice($invoice,$user);
                $entityManager->persist($invoice);
                $entityManager->flush();
                // Persist and flush the entities
            return $this->redirectToRoute('front_user_invoice_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('Front/user/invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_user_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}
