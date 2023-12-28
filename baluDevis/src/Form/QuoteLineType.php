<?php

namespace App\Form;

use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Repository\QuoteRepository;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('quantity', TextType::class)
            ->add('unitPrice')
            //->add('subtotal',TextType::class,[
                 // Adjust this divisor based on your currency ])
            ->add('quote', EntityType::class, [
                'label' => 'Quote',
                'class' => Quote::class,
                'choice_label' => 'id',
                'multiple' => false,
                'expanded' => false,
                'query_builder' => fn (QuoteRepository $quoteRepository) => $quoteRepository->createQueryBuilder('q')->orderBy('q.id', 'ASC'),  
             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuoteLine::class,
        ]);
    }
      public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        // Calculate subtotal
        $quantity = $form->get('quantity')->getData();
        $unitPrice = $form->get('unitPrice')->getData();
        $subtotal = $quantity * $unitPrice;

        // Set the calculated subtotal to the form
        $form->get('subtotal')->setData($subtotal);
    }
}
