<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProduitFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('unit',ChoiceType::class, [
                    'choices'  => [
                        'Kg' => 'kg',
                        'Litre' => 'litre',
                        'Unité' => 'unite',
                        'Autre'=>'autre'
                              ],
                    ])
            ->add('quantity')
            ->add('image',FileType::class,array('data_class' => null))
            ->add('price')
            ->add('category',EntityType::class, [
                'class' => Category::class,
                'choice_label' => function($Category){ // function qui recupere les infos de l'utilisateur
                    return $Category->getName();
                }
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
