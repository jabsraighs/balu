<?php

namespace App\Form;

use App\Entity\Quote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FilterSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status',ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [
                    "en cours" =>  "en cours" ,
                    "valider" =>  "valider",
                    "refuser" => "refuser"
                ],
                'multiple' => false,
                'expanded' => true,
            
          ]);
            //   ->add('createdAt',::class,[
            //      'required' => false,
            //      'label' => 'createdAt',
            //      'widget' => 'single_text',
            //      'input' => 'datetime_immutable'
            //  ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
            'method' => 'get',
            'csrf_protection' => false,
            'clients' => [],
        ]);
    }
}
