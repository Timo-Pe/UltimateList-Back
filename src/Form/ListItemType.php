<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\ListItem;
use App\Entity\Mode;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('item_status', IntegerType::class, [
                'label' => 'Statut'
            ])
            ->add('item_comment', TextType::class, [
                'label' => 'Commentaire',
                'required' => false,
            ])
            ->add('item_rating', IntegerType::class, [
                'label' => 'Note',
                'required' => false,
            ])
            ->add('mode', EntityType::class, [
                'class' => Mode::class,
                'label' => 'Mode'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'User'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ListItem::class,
        ]);
    }
}
