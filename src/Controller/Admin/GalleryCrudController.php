<?php

namespace App\Controller\Admin;

use App\Entity\Gallery;
use App\Service\AwsImageService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class GalleryCrudController extends AbstractCrudController
{
    public function __construct(
        AwsImageService $awsImageService
    ) {
        $this->awsImageService = $awsImageService;
    }

    public static function getEntityFqcn(): string
    {
        return Gallery::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Galeries')
            ->setDefaultSort(['id' => 'DESC'])
            ->setDefaultSort(['id' => 'DESC', 'name' => 'ASC', 'createdAt' => 'DESC'])
            ->setPaginatorPageSize(30);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('name')
            ->add(BooleanFilter::new('isActive'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions

            ->add(Crud::PAGE_INDEX, Action::DETAIL)->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])->setPermission(Action::DELETE, 'ROLE_ADMIN')
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)->setPermission(Action::NEW, 'ROLE_ADMIN')

            ->update(
                Crud::PAGE_INDEX,
                Action::DETAIL,
                fn (Action $action) => $action->setIcon('mdi mdi-eye')->setLabel(false)
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::EDIT,
                fn (Action $action) => $action->setIcon('mdi mdi-square-edit-outline')->setLabel(false)
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::DELETE,
                fn (Action $action) => $action->setIcon('mdi mdi-delete')->setLabel(false)
            );
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('user', 'Par')->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getUser() ? $entity->getUser()->getFullname() : '';
            }),
            TextField::new('name', 'Titre'),
            TextEditorField::new('description', 'Description'),
            DateField::new('createdAt', 'Publiée')->hideOnForm(),
            DateField::new('updatedAt', 'Mise à jour')->hideOnForm(),
            BooleanField::new('is_active', 'Status'),
            BooleanField::new('position', 'position'),
            BooleanField::new('is_protect', 'is_protect'),
            TextField::new('created_at_password', 'created_at_password')->hideOnIndex(),
            TextField::new('slug', 'slug')->hideOnIndex(),
        ];
    }
}
