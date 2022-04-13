<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\ListItem;
use App\Entity\Mode;
use App\Entity\Platform;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('release_date')
            ->add('productor')
            ->add('autor')
            ->add('host')
            ->add('developer')
            ->add('editor')
            ->add('image')
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
