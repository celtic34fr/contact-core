<?php

namespace Celtic34fr\ContactCore\Form;

use Celtic34fr\ContactCore\FormEntity\SysRelationCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class SysRelationsCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => "Nom de la catégorie",
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new NotNull(),
            ],
        ])
        ->add('description', TextType::class, [
            'label' => "Description de la catégorie",
            'required' => false,
        ])
        ->add('record', SubmitType::class, [
            'label' => 'Enregistrer',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SysRelationCategory::class,
        ]);
    }
}