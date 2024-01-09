<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Invoice;
use App\Form\QuoteLineType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $client = $options['client'];
        $builder
            ->add('dueDate', DateType::class,[
                'label' => 'dueDate',
                'widget' => 'single_text',
                'input' => 'datetime_immutable'
            ])
            ->add('paymentStatus',ChoiceType::class, [
                'label' => 'payment Status',
                'choices' => [
                    "waiting " =>  "waiting" ,
                    "paid" =>  "paid",
                    "unpaid" => "unpaid"
                ],
                'multiple' => false,
                'expanded' => true,
            ])->add('tva',ChoiceType::class, [
                'label' => 'Tva',
                'choices' => [
                    "0%" =>  "0" ,
                    "10%" =>  "0.10",
                    "20" => "0.20"
                ],
                'multiple' => false,
                'expanded' => true,
            ])
                ->add('client', EntityType::class, [
                'label' => 'Client',
                'class' => Client::class,
                'choice_label' => 'email',
                'choices' => $client,
                'multiple' => false,
                'expanded' => false,
             ])
             ->add('quoteLines', CollectionType::class, [
                'required' => true,
                'entry_type' => QuoteLineType::class,
                'label' => 'QuoteLines',
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false, // Set to false to use the setter method for Quote::setQuoteLines

             ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
            'client' => [],
        ]);
    }
}
