<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\ListItem;
use App\Entity\Mode;
use App\Entity\Platform;
use App\Entity\Tag;
use App\Repository\ListItemRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('description', TextType::class)
            ->add('release_date', DateType::class, [
                'label' => 'Sortie',
                'widget' => 'single_text'
            ])
            ->add('productor', TextType::class, [
                'label' => 'Producteur',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Podcast ou Film uniquement'
                ]
            ])
            ->add('autor', TextType::class, [
                'label' => 'Auteur / Réalisateur',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Podcast ou Film uniquement'
                ]
            ])
            ->add('host', TextType::class, [
                'label' => 'Animateur / Acteur',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Podcast ou Film uniquement'
                ]
            ])
            ->add('developer', TextType::class, [
                'label' => 'Développeur',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Jeu Vidéo uniquement'
                ]
            ])
            ->add('editor', TextType::class, [
                'label' => 'Editeur',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Jeu Vidéo uniquement'
                ]
            ])
            ->add('image', TextType::class)
            ->add('background_image', TextType::class, [
                'required' => false,
                'help' => 'Si laissé vide, la background image sera l\'image de l\'item',
                ])

            ->add('mode', EntityType::class, [
                'class' => Mode::class,
                'label' => 'Mode'
            ])
            ->add('platforms', EntityType::class, [
                'class' => Platform::class,
                'label' => 'Plateformes',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'label' => 'Tags',
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}
