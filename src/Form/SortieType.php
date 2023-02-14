<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,  [
                'label' => 'Nom de la sortie'
            ])
            ->add('datedebut', DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie : '
            ])
            ->add('duree', TimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Durée : '
            ])
            ->add('datecloture', DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'label' => "Date limite d'inscription : "
            ])
            ->add('nbinscriptionsmax')
            ->add('descriptioninfos',TextareaType::class)
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom_lieu',
                'label' => 'Lieu : '
            ])
            ->add('latitude', TextType::class, [
                'mapped' => false,
                'label' => 'Latitude : '
            ])
            ->add('longitude', TextType::class, [
                'mapped' => false,
                'label' => 'Longitude : '
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
