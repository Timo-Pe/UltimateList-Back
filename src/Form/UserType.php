<?php

namespace App\Form;

use App\Entity\User;
use FOS\RestBundle\Validator\Constraints\Regex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class,[
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 5])
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                // On récupère le form depuis l'event (pour travailler avec)
                $form = $event->getForm();
                // On récupère le user mappé sur le form depuis l'event
                $user = $event->getData();

                // On conditionne le champ "password"
                // Si user existant, il a id non null
                if ($user->getId() !== null) {
                    // Edit
                    $form->add('password', null, [
                        // Pour le form d'édition, on n'associe pas le password à l'entité
                        // @link https://symfony.com/doc/current/reference/forms/types/form.html#mapped
                        'label' => 'Mot de passe',
                        'mapped' => false,
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'Laissez vide si inchangé'
                        ]
                    ]);
                } else {
                    // New
                    $form->add('password', null, [
                        // En cas d'erreur du type
                        // Expected argument of type "string", "null" given at property path "password".
                        // (notamment à l'edit en cas de passage d'une valeur existante à vide)
                        'label' => 'Mot de passe',
                        'empty_data' => '',
                        'help' => 'Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial',
                        // On déplace les contraintes de l'entité vers le form d'ajout
                        'constraints' => [
                            new NotBlank(),
                            new Regex(
                                "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
                                "Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
                            ),
                        ],
                    ]);
                }
            })
            ->add('email', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ],
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
