<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', EntityType::class, [
                'label' => 'Ville : ',
                'class' => City::class,
                'choice_label' => 'name',
            ])
            ->add('name', TextType::class, [
                'label' => 'Lieu',
                'attr' => [
                    'placeholder' => ' ',
                 ]
            ])
            ->add('street',TextType::class, [
                'label' => 'Rue',
                'attr' => [
                    'placeholder' => ' ',
                 ]
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
                'attr' => [
                    'placeholder' => ' ',
                 ]
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
                'attr' => [
                    'placeholder' => ' ',
                 ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
