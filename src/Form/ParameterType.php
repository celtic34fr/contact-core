<?php

namespace Celtic34fr\ContactCore\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Celtic34fr\ContactCore\FormEntity\ParameterFE;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => "Clé d'access",
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new NotNull(),
                new Length([
                    'min' => 4, 'minMessage' => "La clé doit faire au moins 4 caractères",
                    'max' => 16, "maxMessage" => "La clé doit faire au plus 16 caractères"
                ])
            ],
        ])
        ->add('description', TextType::class, [
            'label' => "Fonctionnalité",
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new NotNull(),
                new Length([
                    'min' => 4, 'minMessage' => "La description doit faire au moins 4 caractères",
                    'max' => 255, "maxMessage" => "La description doit faire au plus 255 caractères"
                ])
            ],
        ])
        ->add('values', CollectionType::class, [
            'entry_type' => TextType::class,
            'allow_add' => true,
            'allow_delete' => true,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ParameterFE::class,
        ]);
    }

}
