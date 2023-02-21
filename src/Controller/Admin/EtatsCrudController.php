<?php

namespace App\Controller\Admin;

use App\Entity\Etats;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class EtatsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Etats::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
