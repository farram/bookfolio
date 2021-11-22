<?php

namespace App\Controller\Admin;

use App\Entity\HairColor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HairColorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HairColor::class;
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
