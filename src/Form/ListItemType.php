<?php

namespace App\Form;

use App\Entity\ListItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('item_added_at')
            ->add('item_status')
            ->add('item_comment')
            ->add('item_rating')
            ->add('items')
            ->add('mode')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ListItem::class,
        ]);
    }
}
