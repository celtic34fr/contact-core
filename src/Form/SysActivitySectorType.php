<?php

namespace Celtic34fr\ContactCore\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class SysActivitySectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => "Nom du secteur d'activité",
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
        ->add('parent_id', HiddenType::class, [
            'required' => false,
        ])
        ->add('record', SubmitType::class, [
            'label' => 'Enregistrer',
        ])
        ;
    }
}