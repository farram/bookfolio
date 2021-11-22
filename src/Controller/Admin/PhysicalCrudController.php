<?php

namespace App\Controller\Admin;

use App\Entity\Physical;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PhysicalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Physical::class;
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
