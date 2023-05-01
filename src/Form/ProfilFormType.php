<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', type: EmailType::class, options: [
                'required' => true,
            ])
            ->add('pseudo', type: TextType::class, options: [
                'required' => true,
            ])
            ->add('lastname', type: TextType::class, options: [
                'required' => true,
            ])
            ->add('firstname', type: TextType::class, options: [
                'required' => true,
            ])
            ->add('telephone', type: TextType::class, options: [
                'required' => true,
            ])->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                ])
            ->add('password', type: RepeatedType::class, options: [
                'type' => PasswordType::class,
                'required' => false,
                'mapped' => false,
                'first_options'  => [
                    'label' => 'Nouveau mot de passe',
                    'hash_property_path' => 'password',
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
                'mapped' => false,
                'required' => false,
                'label' => 'Ancien mot de passe',
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
