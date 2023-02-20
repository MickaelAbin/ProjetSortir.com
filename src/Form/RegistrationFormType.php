<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RegistrationFormType extends AbstractType
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
                'allow_delete'  => false
            ])
            ->add('current_password', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'required' => false,
                'mapped' => false, // ne mappe pas ce champ à l'entité User
            ])

            ->add('plainPassword', RepeatedType::class, [
                'label'=>'Mot de passe : ',
                'type'=> PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => ['label' => 'Mot de passe : '],
                'second_options' => ['label' => 'Confirmer le mot de passe : '],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
