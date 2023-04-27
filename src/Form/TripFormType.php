<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Place;
use App\Entity\Trip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
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
                'label' => 'Nombre de places : '])
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
                'class' => City::class,//va chercher les villes dans l'entité City pour les afficher
                'choice_label' => 'name',
            ])*/

            ->add('place', EntityType::class, [
                'label' => 'Lieu : ',
                'class' => Place::class,//va chercher les villes dans l'entité Place pour les afficher
                'choice_label' => 'name',
            ])

            ->add('add', ButtonType::class, [ //bouton d'ajouter d'un nouveau lieu
                'label' => '+',
                'attr' => [
                    'class' => 'add-button',
                    'submit' => 'button',
                ],
            ])
            /*->add('latitude', EntityType::class, [
                'label' => 'Latitude : ',
                'class' => Place::class,
            ])*/

            /*->add('longitude', EntityType::class, [
                'label' => 'Longitude : ',
                'class' => Place::class,
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
