<?php

namespace App\Controller\Admin;

use App\Entity\Plan;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Plan::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Formules abonnements');
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            TextField::new('plan_name', 'Nom'),
            TextField::new('plan_price', 'Tarif en décimal'),
            BooleanField::new('is_highlight', 'Mis en avant'),
            TextField::new('icon', 'Icon'),
            IdField::new('publication', 'Publication'),
            TextField::new('total', 'Total'),
            TextField::new('id_price_api', 'ID DE L\'API'),
            IdField::new('position', 'Position'),

            FormField::addPanel()->setHelp('Détails'),
            AssociationField::new('planDetails', 'Détails')->hideOnIndex(),
        ];

        return $fields;
    }
}
