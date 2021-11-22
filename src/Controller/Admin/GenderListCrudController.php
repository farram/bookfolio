<?php

namespace App\Controller\Admin;

use App\Entity\GenderList;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GenderListCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GenderList::class;
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
