<?php

namespace App\Form;

use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'label' => 'Montant',
                'currency' => 'EUR',
                'scale' => 2,
                'attr' => [
                    'class' => 'form-control',
                    'max' => $options['remaining_amount'],
                    'placeholder' => 'Montant du paiement'
                ],
            ])
            ->add('datePaid', DateTimeType::class, [
                'label' => 'Date de paiement',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('method', ChoiceType::class, [
                'label' => 'Méthode de paiement',
                'choices' => [
                    'Carte bancaire' => 'card',
                    'Virement bancaire' => 'bank_transfer',
                    'Espèces' => 'cash',
                    'Chèque' => 'check',
                    'PayPal' => 'paypal',
                    'Autre' => 'other'
                ],
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
            'invoice' => null,
            'remaining_amount' => 0
        ]);
    }
}