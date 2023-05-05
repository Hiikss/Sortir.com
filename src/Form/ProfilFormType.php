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
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', type: TextType::class, options: [
                'required' => true,
                'label' => 'Pseudo',
                'attr' => [
                    'placeholder' => ' ',
                    'maxlength' => 50
                ]
            ])
            ->add('firstname', type: TextType::class, options: [
                'required' => true,
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => ' ',
                    'maxlength' => 50
                ]
            ])
            ->add('lastname', type: TextType::class, options: [
                'required' => true,
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => ' ',
                    'maxlength' => 50
                ]
            ])
            ->add('telephone', type: TelType::class, options: [
                'required' => true,
                'attr' => [
                    'placeholder' => ' ',
                ]
            ])
            ->add('email', type: EmailType::class, options: [
                'required' => true,
                'attr' => [
                    'placeholder' => ' ',
                ]
            ])
            ->add('password', type: RepeatedType::class, options: [
                'type' => PasswordType::class,
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
                'attr' => [
                    'autocomplete' => 'new-password', 'minlength' => 6
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'first_options'  => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => ['placeholder' => ' ', 'minlength' => '6'],
                    'hash_property_path' => 'password',

                ],
                'second_options' => [
                    'label' => 'Confirmer mot de passe',
                    'attr' => ['placeholder' => ' ', 'minlength' => '6']
                ]
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Choisir une image de profil',
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
            ])
            ->add('oldPassword', type: PasswordType::class, options: [
                'mapped' => false,
                'required' => true,
                'label' => 'Ancien mot de passe *',
                'attr' => [
                    'autocomplete' => 'old-password',
                    'placeholder' => ' ', 'minlength' => '6',
                    'autofocus' => true
                ],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                    new UserPassword(
                        message: 'Veuillez saisir votre mot de passe actuel.'
                    ),
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
