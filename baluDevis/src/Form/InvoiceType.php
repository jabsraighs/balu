<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\Quote;
use Doctrine\ORM\QueryBuilder;
use App\Repository\QuoteRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $quotes = $options['quotes'];
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
            ])

            ->add('quote', EntityType::class, [
            'class' => Quote::class,
            'choice_label' => 'name',
            'choices' => $quotes,
            'multiple' => false,
            'expanded' => false,

            ])
            ->add("valider",SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
            'quotes' => [],
        ]);
    }
}
