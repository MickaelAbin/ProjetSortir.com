<?php

namespace App\Form;

use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nomSite',
                'label' => 'Site : '
            ])
            ->add('recherche', TextType::class,[
                'label' => 'Le nom de la sortie contient',
                'required' => false
            ])
            ->add('dateDepart', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Entre le : ',
                'required' => false
            ])
            ->add('dateFin', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'et le : ',
                'required' => false
            ])
            ->add('organise', CheckboxType::class, [
                'label' => 'Sorties que j\'organise',
                'required' => false
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je participe',
                'required' => false
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'label' => 'Sortie auxquelles je ne participe pas',
                'required' => false
            ])
            ->add('passe', CheckboxType::class, [
                'label' => 'Sortie passées',
                'required' => false
            ])
            ->add('valide', SubmitType::class, [
                'label' => 'Rechercher'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
