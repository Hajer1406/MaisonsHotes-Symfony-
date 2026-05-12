<?php

namespace App\Form;

use App\Entity\MaisonSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaisonSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', ChoiceType::class, [
                'label' => 'Ville',
                'choices' => $options['city_choices'],
                'placeholder' => 'Toutes les villes',
                'required' => false,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('minPrice', NumberType::class, [
                'label' => 'Prix minimum',
                'required' => false,
                'html5' => true,
                'scale' => 2,
                'invalid_message' => 'Veuillez saisir un prix valide.',
                'attr' => [
                    'placeholder' => 'Prix min',
                    'min' => 0,
                    'step' => '0.01',
                    'inputmode' => 'decimal',
                    'class' => 'form-control'
                ]
            ])
            ->add('maxPrice', NumberType::class, [
                'label' => 'Prix maximum',
                'required' => false,
                'html5' => true,
                'scale' => 2,
                'invalid_message' => 'Veuillez saisir un prix valide.',
                'attr' => [
                    'placeholder' => 'Prix max',
                    'min' => 0,
                    'step' => '0.01',
                    'inputmode' => 'decimal',
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MaisonSearch::class,
            'csrf_protection' => false,
            'method' => 'GET',
            'city_choices' => [],
        ]);
    }
}