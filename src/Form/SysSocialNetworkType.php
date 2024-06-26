<?php

namespace Celtic34fr\ContactCore\Form;

use Celtic34fr\ContactCore\FormEntity\SysSocialNetwork;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class SysSocialNetworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => "Nom du réseau social",
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new NotNull(),
            ],
        ])
        ->add('urlPage', TextType::class, [
            'label' => "Lien vers la page du réseau social",
            'required' => false,
        ])
        ->add('logoSN', FileType::class, [
            'required' => false,
            'label' => '',
            'multiple' => false,
            'mapped' => false,
        ])
        ->add('logoID', HiddenType::class, [
            'required' =>false,
        ])
        ->add('record', SubmitType::class, [
            'label' => 'Enregistrer',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SysSocialNetwork::class,
        ]);
    }
}