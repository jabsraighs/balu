<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\CallbackTransformer;
class CompanyType extends AbstractType
{
     public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('siret', TextType::class, [
                'attr' => ['maxlength' => 14, 'pattern' => '[0-9]{14}'],
            ])
            ->add('address');

        $builder->get('siret')->addModelTransformer(new CallbackTransformer(
            fn ($siret) => preg_replace('/\s+/', '', $siret), // Transforme l'entité → formulaire
            fn ($siret) => preg_replace('/\s+/', '', $siret)  // Transforme formulaire → entité
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}