<?php

namespace App\Form;

use App\Entity\Client; 
use App\Entity\Quote;
use App\Form\QuoteLineType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class QuoteFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $clients = $options['clients'];
        $builder
            // ->add('createdAt',DateType::class,[
            //     'required' => false,
            //     'label' => 'createdAt',
            //     'widget' => 'single_text',
            //     'input' => 'datetime_immutable'
            // ])
        //     ->add('expiryAt',DateType::class,[
        //         'required' => false,
        //         'label' => 'createdAt',
        //         'widget' => 'single_text',
        //         'input' => 'datetime_immutable'
                
        //     ])
            ->add('status',ChoiceType::class, [
                'required' => false,
                'label' => 'status',
                'choices' => [
                    "en cours" =>  "en cours" ,
                    "valider" =>  "valider",
                    "refuser" => "refuser"
                ],
                'multiple' => false,
                'expanded' => true,
            
          ]);
        //    // ->add('totalAmount')  total amount pas besoin vu que les item et leur sub total qui le determine
        //     ->add('client', EntityType::class, [
        //         'required' => false,
        //         'label' => 'Client',
        //         'class' => Client::class,
        //         'choice_label' => 'email',
        //         'choices' => $clients,
        //         'multiple' => false,
        //         'expanded' => false
        //      ]);

    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
            'clients' => [],
            
        ]);
    }
}
