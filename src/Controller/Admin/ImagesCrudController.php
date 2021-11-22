<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Service\AwsImageService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ImagesCrudController extends AbstractCrudController
{
    public function __construct(
        AwsImageService $awsImageService
    ) {
        $this->awsImageService = $awsImageService;
    }

    public static function getEntityFqcn(): string
    {
        return Images::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])->setPermission(Action::DELETE, 'ROLE_ADMIN')
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)->setPermission(Action::NEW, 'ROLE_ADMIN');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ->overrideTemplates([
            //     'crud/index' => 'admin/test.html.twig',
            // ])
            // ->overrideTemplate('label/null', 'admin/test.html.twig')
            ->setPageTitle('index', 'Images')
            ->setDefaultSort(['id' => 'DESC'])
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(30);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('ImageName', 'Image')->formatValue(function ($value, $entity) {
                return $this->awsImageService->getPathImageProvider($entity->getImagePath(), 'thumbnail_card');
            }),
            TextField::new('title', 'Titre'),

            AssociationField::new('gallery', 'Gallerie')->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getGallery()->getName();
            }),

            AssociationField::new('user', 'Publiée par')->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getUser() ? $entity->getUser()->getFullname() : '';
            }),

            AssociationField::new('user', 'Book')->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getUser() ? $entity->getUser()->getBook()->getName() : '';
            }),

            AssociationField::new('imageViews', 'V')->hideOnForm()->formatValue(function ($value, $entity) {
                return count($entity->getImageViews());
            }),
            AssociationField::new('imageLikes', 'L')->hideOnForm()->formatValue(function ($value, $entity) {
                return count($entity->getImageLikes());
            }),
            AssociationField::new('imageComments', 'C')->hideOnForm()->formatValue(function ($value, $entity) {
                return count($entity->getImageComments());
            }),

            // ImageField::new('ImageName')->setBasePath($this->awsImageService->getPathFolder($id)),
            // TextField::new('imageName')->setFormTypeOption('disabled', 'disabled'),

            BooleanField::new('isNsfw', 'NSFW'),

            DateField::new('createdAt', 'Publiée le')->hideOnForm(),
        ];
    }
}
