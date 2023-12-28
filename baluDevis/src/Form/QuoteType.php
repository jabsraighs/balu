<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Quote;
use App\Form\QuoteLineType;
use App\Repository\ClientRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('expiryAt',DateType::class,[
                'label' => 'expiryAt',
                'widget' => 'single_text',
                'input' => 'datetime_immutable'
            ])
            ->add('status',ChoiceType::class, [
                'label' => 'status',
                'choices' => [
                    "in progress" =>  "in Progress" ,
                    "validate" =>  "validate",
                    "decline" => "decline"
                ],
                'multiple' => false,
                'expanded' => true,
            ])
           // ->add('totalAmount')  total amount pas besoin vu que les item et leur sub total qui le determine
             ->add('client', EntityType::class, [
                'label' => 'Client',
                'class' => Client::class,
                'choice_label' => 'email',
                'multiple' => false,
                'expanded' => false,
                'query_builder' => fn (ClientRepository $clientRepository) => $clientRepository->createQueryBuilder('c')->orderBy('c.email', 'ASC'),  
             ])
             ->add('quoteLines', CollectionType::class, [
                'entry_type' => QuoteLineType::class, // Assuming QuoteLineType is your QuoteLine form type
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
        ]);
    }
}
