<?php

namespace App\Controller\Admin;

use App\Entity\Etats;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('nom'),
            DateTimeField::new('datedebut'),
            DateField::new('datecloture'),
            NumberField::new('nbinscriptionsmax'),
            AssociationField::new('organisateur')
                ->hideOnIndex(),
            TextField::new('organisateur.pseudo')
                ->onlyOnIndex(),
            AssociationField::new('site')
                ->setFormType(EntityType::class)
                ->setFormTypeOption('class',Site::class)
                ->setFormTypeOption('choice_label', 'nomSite')
                ->hideOnIndex(),
            TextField::new('site.nomSite')
                ->onlyOnIndex(),
           AssociationField::new('etat')
                ->setFormType(ChoiceType::class)
                ->setFormTypeOption('class', Etats::class)
                ->setFormTypeOption('choice_label', 'libelle')
                ->hideOnIndex(),
            TextField::new('etat.libelle')
                ->onlyOnIndex()

        ];
    }

}
