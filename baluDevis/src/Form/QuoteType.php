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


class QuoteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $clients = $options['clients'];
        $builder
            ->add('description',TextType::class,[
                'label' => 'Description',
                'required' => true
            ])
            ->add('expiryAt',DateType::class,[
                'label' => "Date d'expiration",
                'widget' => 'single_text',
                'input' => 'datetime_immutable'
            ])
            ->add('status',ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    "in progress" =>  "en cours" ,
                    "validate" =>  "valider",
                    "decline" => "decline"
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('tva',ChoiceType::class, [
                'label' => 'Taux Tva',
                'choices' => [
                    "0%" =>  "0" ,
                    "10%" =>  "0.10",
                    "20%" => "0.20"
                ],
                'multiple' => false,
                'expanded' => true,
            ])
           // ->add('totalAmount')  total amount pas besoin vu que les item et leur sub total qui le determine
                ->add('client', EntityType::class, [
                'label' => 'Client',
                'class' => Client::class,
                'choice_label' => 'email',
                'choices' => $clients,
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
            'data_class' => Quote::class,
            'clients' => [],
        ]);
    }
}
