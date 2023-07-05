<?php

namespace Celtic34fr\ContactCore\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Celtic34fr\ContactCore\FormEntity\EntrepriseInfosFE;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EntrepriseInfosType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('designation', TextType::class, [
            'label' => 'Son Nom',
            'required' => true,
        ])
        ->add('siren', TextType::class, [
            'label' => 'Votre SIREN',
            'required' => false,
        ])
        ->add('siret', TextType::class, [
            'label' => 'Votre SIRET',
            'required' => false,
        ])
        ->add('courriel', TextType::class, [
            'label' => 'Votre adresse Courriel',
            'required' => false,
        ])
        ->add('reponse', TextType::class, [
            'label' => 'Votre adresse Courriel de réponse',
            'required' => false,
        ])
        ->add('telephone', TextType::class, [
            'label' => "Son téléphone (stardard d'accueil)",
            'required' => false,
        ])
        ->add('logo', FileType::class, [
            'required' => false,
            'label' => '',
            'multiple' => false,
            'mapped' => false,
        ])
        ->add('logoID', HiddenType::class, [
            'required' =>false,
        ])
        ->add('record', SubmitType::class, [
            'label' => 'Enregistrer les informations',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EntrepriseInfosFE::class,
        ]);
    }
}
