<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Quote;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class QuoteFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $clients = $options['clients'];
        $builder
            ->add('createdAt',DateType::class,[
                'label' => 'createdAt',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => false
            ])
            ->add('expiryAt',DateType::class,[
                'label' => 'expiryAt',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => false
            ])
            ->add('status',ChoiceType::class, [
                'label' => 'status',
                'choices' => [
                    "in progress" =>  "in Progress" ,
                    "validate" =>  "valider",
                    "decline" => "decline"
                ],
                'multiple' => false,
                'expanded' => true,
                'required' => false
            ])
           // ->add('totalAmount')  total amount pas besoin vu que les item et leur sub total qui le determine
                ->add('client', EntityType::class, [
                'label' => 'Client',
                'class' => Client::class,
                'choice_label' => 'email',
                'choices' => $clients,
                'multiple' => false,
                'expanded' => false,
                'required' => false
             ]);

    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
            'clients' => [],
        ]);
    }
}
