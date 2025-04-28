<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AcceptInviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Email prérempli et désactivé
        $builder
            ->add('email', EmailType::class, [
                'mapped'   => false,
                'data'     => $options['email_readonly'],
                'disabled' => true,
                'label'    => 'Adresse email',
            ])
            ->add('fullname', TextType::class, [
                'label' => 'Nom complet',
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped'   => false,
                'label'    => 'Mot de passe',
                'help'     => 'Votre mot de passe doit comporter au moins 8 caractères.',
                'attr'     => ['autocomplete' => 'new-password'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => User::class,
            'email_readonly'=> null,
        ]);

        $resolver->setAllowedTypes('email_readonly', ['null', 'string']);
    }
}
