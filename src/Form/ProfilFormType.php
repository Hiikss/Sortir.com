<?php

namespace App\Form;

use App\Entity\User;
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
            ])
            ->add('save', type: SubmitType::class, options: [
                'label' => 'Enregistrer'
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
