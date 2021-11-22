<?php

namespace App\Controller\Admin;

use App\Entity\Ethnicity;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class EthnicityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ethnicity::class;
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
