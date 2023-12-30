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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dueDate', DateType::class,[
                'label' => 'dueDate',
                'widget' => 'single_text',
                'input' => 'datetime_immutable'
            ])
            ->add('paymentStatus',ChoiceType::class, [
                'label' => 'paymentstatus',
                'choices' => [
                    "in progress" =>  "in Progress" ,
                    "validate" =>  "validate",
                    "decline" => "decline"
                ],
                'multiple' => false,
                'expanded' => true,
            ])

            ->add('quote', EntityType::class, [
            'class' => Quote::class,
            'choice_label' => 'id',
            'multiple' => false,
            'expanded' => false,
            'query_builder' => function (QuoteRepository $quoteRepository): QueryBuilder {
                return $quoteRepository->createQueryBuilder('q')
                    ->andWhere('q.status = :status')
                    ->setParameter('status', 'valider')
                    ->orderBy('q.id', 'ASC');
            }

            ])
            ->add('quote', CollectionType::class, [
                'required' => true,
                'entry_type' => QuoteType::class,
                'label' => 'Quote',
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
        ]);
    }
}
