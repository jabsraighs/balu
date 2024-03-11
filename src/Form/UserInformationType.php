<?php

namespace App\Form;

use App\Entity\UserInformation;
use App\Form\DataTransformer\PhoneTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class UserInformationType extends AbstractType
{
    public function __construct(
        private PhoneTransformer $transformer,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label'=> 'Prénom',
                'attr' => [
                    'placeholder' => 'Jean',
                ],
            ])
            ->add('lastname', TextType::class, [
                'label'=> 'Nom',
                'attr' => [
                    'placeholder' => 'Dupont',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => "Email",
                'attr' => [
                    'placeholder' => 'example@mail.com',
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'required' => true,
                'attr' => [
                    'placeholder' => '6 99 20 20 20',
                ],
            ]);

        // Add the data transformer to the phone field
        $builder->get('phone')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInformation::class,
        ]);
    }
}
