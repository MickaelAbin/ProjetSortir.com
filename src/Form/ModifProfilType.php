<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ModifProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,['label'=>'Email : '])
            ->add('nom',TextType::class,['label'=>'Nom : '])
            ->add('prenom',TextType::class,['label'=>'Prénom: '])
            ->add('telephone', TelType::class,['label'=>'Télephone : '])
            ->add('pseudo',TextType::class,['label'=>'Pseudo : '])
            ->add('site',
                EntityType::class,
                [
                    'label'=>'Ville de rattachement : ',
                    "class" => Site::class,
                    "choice_label" => "nom_site"
                ]
            )
            ->add('imageFile',VichFileType::class,[
                'label'=> 'Photo : ',
                'required' => false,
                'download_uri' => false,
                'allow_delete'  => false,
            ])
            ->add('current_password', PasswordType::class, [

                'label' => 'Mot de passe actuel',
                'required' => false,

                'mapped' => false, // ne mappe pas ce champ à l'entité User
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}