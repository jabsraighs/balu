<?php

namespace App\Form;

use App\Entity\User;
use PHPUnit\Framework\Constraint\IsTrue as ConstraintIsTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('email',EmailType::class,[
                'label'=>'votre email',
                'attr' => [
                    'class'=> 'mt-5',
                    'placeholder' => 'Entrez votre email'
                ],
                'required' => 'false'
            ])
            ->add('roles')
            ->add('plainPassword',PasswordType::class,[
                'required' => false,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'votre mot de passe doit avoir {{ limit }} caractère',
                        'max' => 4096,
                    ])
                ]
            ])
            ->add('agreeTerms',CheckboxType::class,[
                'label' => 'accepter les conditions',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'vous devez accepter les conditions'
                    ]),
                ],
                'required' => false
            ])
        ;   
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
