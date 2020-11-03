<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName', TextType::class, 
        [
            'label' => 'Prénom',
            'attr' => array(
                'class' => 'input100'
            )
        ])
        ->add('lastName', TextType::class, 
        [
            'label' => 'Nom',
            'attr' => array(
                'class' => 'input100'
            )
        ])
            ->add('email', TextType::class, 
            [
                'label' => 'Adresse email',
                'attr' => array(
                    'class' => 'input100'
                )
            ])
            ->add('password', PasswordType::class, 
            [
                'label' => 'Entrez votre mot de passe',
                'attr' => array(
                    'class' => 'input100'
                )
            ])
            ->add('address_line1', TextType::class, 
            [
                'label' => 'Adresse ligne 1',
                'attr' => array(
                    'class' => 'input100'
                )
            ])
            ->add('address_line2', TextType::class, [
                'label' => 'Adresse ligne 2',
                'attr' => array(
                    'class' => 'input100'
                )
            ] )
            ->add('zip_code', TextType::class, [
                'label' => 'Code postal',
                'attr' => array(
                    'class' => 'input100'
                )
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => array(
                    'class' => 'input100'
                )
            ])
            ->add('region', TextType::class, [
                'label' => 'Région',
                'attr' => array(
                    'class' => 'input100'
                )
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'M\'inscrire',
                'attr' => array(
                    'class' => 'login100-form-btn'
                )
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
