<?php

namespace App\Controller\Admin;

use App\Entity\PlanFeature;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlanFeatureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PlanFeature::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Feature');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('description'),
        ];
    }
}
