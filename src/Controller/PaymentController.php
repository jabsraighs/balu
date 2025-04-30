<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Invoice;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/payment')]
#[IsGranted('ROLE_COMPANY')]
final class PaymentController extends AbstractController
{
    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(PaymentRepository $paymentRepository): Response
    {
        $company = $this->getUser()->getCompany();
        $payments = $paymentRepository->findByCompany($company);
        
        return $this->render('payment/index.html.twig', [
            'payments' => $payments,
        ]);
    }

    #[Route('/new/{invoice}', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que la facture appartient à l'entreprise de l'utilisateur
        if ($invoice->getCompany()->getId() !== $this->getUser()->getCompany()->getId()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à ajouter un paiement à cette facture.');
        }

        $payment = new Payment();
        $payment->setInvoice($invoice);
        $payment->setDatePaid(new \DateTime());
        
        // Montant restant à payer
        $totalPaid = 0;
        foreach ($invoice->getPayments() as $existingPayment) {
            $totalPaid += (float)$existingPayment->getAmount();
        }
        $remainingAmount = (float)$invoice->getTotalAmount() - $totalPaid;
        $payment->setAmount((string)$remainingAmount);
        
        $form = $this->createForm(PaymentType::class, $payment, [
            'invoice' => $invoice,
            'remaining_amount' => $remainingAmount
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($payment);
            
            // Mettre à jour le statut de la facture
            $this->updateInvoiceStatus($invoice, $entityManager);
            
            $entityManager->flush();

            $this->addFlash('success', 'Le paiement a été enregistré avec succès.');
            return $this->redirectToRoute('app_invoice_show', ['id' => $invoice->getId()]);
        }

        return $this->render('payment/new.html.twig', [
            'payment' => $payment,
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): Response
    {
        // Vérifier que le paiement appartient à l'entreprise de l'utilisateur
        if ($payment->getInvoice()->getCompany()->getId() !== $this->getUser()->getCompany()->getId()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à voir ce paiement.');
        }
        
        return $this->render('payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_payment_delete', methods: ['POST'])]
    public function delete(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que le paiement appartient à l'entreprise de l'utilisateur
        if ($payment->getInvoice()->getCompany()->getId() !== $this->getUser()->getCompany()->getId()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce paiement.');
        }
        
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->getPayload()->getString('_token'))) {
            $invoice = $payment->getInvoice();
            $entityManager->remove($payment);
            
            // Mettre à jour le statut de la facture
            $this->updateInvoiceStatus($invoice, $entityManager);
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Le paiement a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_invoice_show', ['id' => $payment->getInvoice()->getId()]);
    }
    
    /**
     * Met à jour automatiquement le statut d'une facture en fonction des paiements
     */
    private function updateInvoiceStatus(Invoice $invoice, EntityManagerInterface $entityManager): void
    {
        $totalAmount = (float)$invoice->getTotalAmount();
        $totalPaid = 0;
        
        foreach ($invoice->getPayments() as $payment) {
            $totalPaid += (float)$payment->getAmount();
        }
        
        // Si le montant total est payé
        if ($totalPaid >= $totalAmount) {
            $invoice->setStatus('paid');
            $invoice->setDatePaid(new \DateTime());
        } 
        // Si paiement partiel
        elseif ($totalPaid > 0) {
            $invoice->setStatus('partial');
        } 
        // Si la date d'échéance est dépassée
        elseif ($invoice->getDateDue() < new \DateTime()) {
            $invoice->setStatus('overdue');
        }
        // Sinon, reste en attente
        else {
            $invoice->setStatus('pending');
        }
        
        $entityManager->persist($invoice);
    }
}