<?php

namespace App\Controller\Admin;

use App\Entity\Avantages;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AvantagesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Avantages::class;
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
