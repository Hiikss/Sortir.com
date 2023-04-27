<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'a-btn'
                ]
                ])
            ->add('cancel', SubmitType::class, ['label' => 'Annuler'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
