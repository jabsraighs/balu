<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Company;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ProductType extends AbstractType
{
    private $authorizationChecker;
    private $entityManager;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker = null, 
        EntityManagerInterface $entityManager = null
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('unitPrice');
            
        // Pour les administrateurs, ajouter d'abord le champ company
        if (($this->authorizationChecker && $this->authorizationChecker->isGranted('ROLE_ADMIN')) || $options['is_admin']) {
            $builder->add('company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'name',
                'required' => true,
                'placeholder' => 'Sélectionnez une entreprise',
                'attr' => [
                    'class' => 'company-select', // Classe pour cibler avec JS
                    'data-categories-url' => $options['categories_url'] // URL pour l'AJAX
                ]
            ]);
            
            // Ajouter le champ catégorie en dernier pour qu'il puisse être dynamique
            $formModifier = function (FormInterface $form, Company $company = null) {
                $categories = null === $company 
                    ? [] 
                    : $this->entityManager->getRepository(Category::class)->findBy(['company' => $company]);
                
                $form->add('category', EntityType::class, [
                    'class' => Category::class,
                    'choices' => $categories,
                    'choice_label' => 'name',
                    'required' => true,
                    'placeholder' => 'Sélectionnez d\'abord une entreprise',
                    'attr' => ['class' => 'category-select'] // Classe pour cibler avec JS
                ]);
            };
            
            // Ajouter initialement le champ catégorie
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
                $product = $event->getData();
                $company = $product->getCompany();
                $formModifier($event->getForm(), $company);
            });
            
            // S'abonner à l'événement lorsque l'entreprise est modifiée
            $builder->get('company')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($formModifier) {
                    $company = $event->getForm()->getData();
                    $formModifier($event->getForm()->getParent(), $company);
                }
            );
        } else {
            // Utilisateur standard, montrer uniquement les catégories de sa propre entreprise
            $builder->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->where('c.company = :company')
                        ->setParameter('company', $options['user_company'] ?? null)
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
                'required' => true,
                'placeholder' => 'Sélectionnez une catégorie'
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'is_admin' => false,
            'user_company' => null,
            'categories_url' => null
        ]);

        $resolver->setAllowedTypes('is_admin', 'bool');
        $resolver->setAllowedTypes('user_company', ['null', 'App\Entity\Company']);
        $resolver->setAllowedTypes('categories_url', ['null', 'string']);
    }
}
