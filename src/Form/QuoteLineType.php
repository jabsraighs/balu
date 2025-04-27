<?php
namespace App\Form;

use App\Entity\QuoteLine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productName', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'product-name-input']
            ])
            ->add('productDescription', TextType::class, [
                'required' => false,
            ])
            ->add('description', TextType::class, [
                'required' => true,
            ])
            ->add('quantity', NumberType::class, [
                'required' => true,
                'html5' => true,
                'attr' => ['min' => 1, 'value' => 1]
            ])
            ->add('unitPrice', NumberType::class, [
                'required' => true,
                'html5' => true,
                'scale' => 2,
                'attr' => ['min' => 0, 'step' => '0.01']
            ])
            ->add('discount', NumberType::class, [
                'required' => false,
                'html5' => true,
                'attr' => ['min' => 0, 'max' => 100, 'value' => 0]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuoteLine::class,
        ]);
    }
}