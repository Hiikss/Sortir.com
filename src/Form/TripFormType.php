<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Trip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TripFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de la sortie : '])
            ->add('startDateTime', DateTimeType::class, [
                'label' => 'Date et heure de la sortie : ',
                'widget' => 'single_text',
                ])

            ->add('limitEntryDate', DateType::class, [
                'label' => 'Date limite d\'inscription',
                'widget' => 'single_text',
                ])

            ->add('maxRegistrationsNb', IntegerType::class, [
                'label' => 'Nombre de places : ',
                 'attr' => [
                    'min' => 0,
                ],
            ])

            ->add('duration', IntegerType::class, [
                'label' => 'Durée (en minutes) : ',
                'attr' => [
                    'min' => 0, //durée minimum
                    'step' => 1, //par pas de 1
                ],
            ])
            ->add('tripInfos', TextareaType::class, [
                'label' => 'Description et infos : ',
            ])

            ->add('campus', EntityType::class, [
                'label' => 'Campus : ',
                'class' => Campus::class,
                'choice_label' => 'name',
                ])

            ->add('place', PlaceType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
