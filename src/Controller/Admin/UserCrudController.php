<?php

namespace App\Controller\Admin;

use App\Entity\Site;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            textField::new('pseudo'),
            EmailField::new('email'),
            textField::new('password')
                ->onlyOnForms()
                ->setFormType(PasswordType::class),
            textField::new('nom'),
            textField::new('prenom'),
            TelephoneField::new('telephone'),
            AssociationField::new('site')
                ->setFormType(EntityType::class)
                ->setFormTypeOption('class',Site::class)
                ->setFormTypeOption('choice_label', 'nomSite')
                ->hideOnIndex(),
            textField::new('site.nomSite')
                ->onlyOnIndex(),
            BooleanField::new('actif')
        ];
    }

}
