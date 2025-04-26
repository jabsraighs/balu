<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Company;
use App\Entity\Quote;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quoteNumber')
            ->add('status')
            ->add('dateCreated', null, [
                'widget' => 'single_text'
            ])
            ->add('dateValidUntil', null, [
                'widget' => 'single_text'
            ])
            ->add('totalAmount')
            ->add('company', EntityType::class, [
                'class' => Company::class,
'choice_label' => 'id',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
'choice_label' => 'id',
            ])
            ->add('customer', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
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
