<?php

namespace App\Controller\Admin;

use App\Entity\PlanDetails;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlanDetailsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PlanDetails::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Détails');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('plan', 'Plan'),
            AssociationField::new('feature', 'Feature'),
            TextField::new('value'),
        ];
    }
}
