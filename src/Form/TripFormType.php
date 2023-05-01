<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Place;
use App\Entity\Trip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Test\IntegrationTestCase;

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
                'label' => 'Nombre de places : '
            ])

            ->add('duration', IntegerType::class, [
                'label' => 'Durée : ',
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

            /*->add('city', EntityType::class, [
                'label' => 'Ville : ',
                'class' => City::class,
                'choice_label' => 'city',
            ])*/

            /*->add('place', EntityType::class, [
                'label' => 'Lieu : ',
                'class' => Place::class,//va chercher les villes dans l'entité Place pour les afficher
                'choice_label' => 'name',
            ])

            ->add('add', ButtonType::class, [ //bouton ajouter un nouveau lieu
                'label' => '+',
                'attr' => [
                    'class' => 'add-button',
                    'submit' => 'button',
                ],
            ])*/

            ->add('place', CollectionType::class, [
                'entry_type' => Place::class, // LieuType est le formulaire pour un lieu
                'allow_add' => true, // autoriser l'ajout de nouveaux lieux
                'by_reference' => false, // obligatoire si vous voulez ajouter de nouveaux objets à la collection
            ])
            ->add('ajouter_lieu', SubmitType::class, [
                'label' => '+',
                'attr' => ['class' => 'btn btn-primary'],
            ])

            ->add('place', EntityType::class, [
                'label' => 'Latitude : ',
                'class' => Place::class,
                'choice_label' => 'latitude',
            ])

            ->add('place', EntityType::class, [
                'label' => 'Longitude : ',
                'class' => Place::class,//
                'choice_label' => 'longitude',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
