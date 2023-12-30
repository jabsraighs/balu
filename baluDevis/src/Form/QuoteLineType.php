<?php

namespace App\Form;

use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Repository\QuoteRepository;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', TextType::class,[
                'label' => 'quantity'
            ])
            ->add('unitPrice', TextType::class,[
                'label' => 'unitPice'
            ]);
            //->add('subtotal',TextType::class,[
                 // Adjust this divisor based on your currency ])

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuoteLine::class,
        ]);
    }

}
