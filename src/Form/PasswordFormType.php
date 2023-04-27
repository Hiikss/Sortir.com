<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', type: RepeatedType::class, options: [
                'type' => PasswordType::class,
                'required' => false,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank(),
                        new NotNull(),
                    ],
                    ],
                'second_options' => [
                    'label' => 'Confirmer mot de passe',
                    'constraints' => [
                        new NotBlank(),
                        new NotNull(),
                    ],
                    ]
            ])
            ->add('oldPassword', type: PasswordType::class, options: [
                'mapped' => true,
                'required' => false,
                'label' => 'Ancien mot de passe',
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('cancel', SubmitType::class, ['label' => 'Annuler'])
        ;
    }

   /* public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }*/
}
