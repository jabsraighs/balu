<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Quote;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quoteNumber')
            ->add('dateCreated', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('dateValidUntil', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'input' => 'datetime_immutable',
                'attr' => [
                    'class' => 'datepicker',
                    'data-date-format' => 'Y-m-d'
                ],
                'format' => 'yyyy-MM-dd',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner un client'
            ])
            ->add('quoteLines', CollectionType::class, [
                'entry_type' => QuoteLineType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
        ]);
    }
}
