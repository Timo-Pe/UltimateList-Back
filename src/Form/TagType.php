<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                $tag = $event->getData();

                // On conditionne le champ "couleur"
                // Si user existant, il a id non null
                if ($tag->getId() !== null) {
                    // Edit
                    $form->add('color', null, [
                        // Pour le form d'édition, on n'associe pas le couleur à l'entité
                        // @link https://symfony.com/doc/current/reference/forms/types/form.html#mapped
                        'label' => 'Couleur'
                    ]);
                } else {
                    // New
                    $form->add('color', null, [
                        // En cas d'erreur du type
                        // Expected argument of type "string", "null" given at property path "couleur".
                        // (notamment à l'edit en cas de passage d'une valeur existante à vide)
                        'label' => 'Couleur',
                        'empty_data' => '#7068F4',
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'Couleur par défaut : #7068F4'
                        ]
                    ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
