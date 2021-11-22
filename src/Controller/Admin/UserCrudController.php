<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\StylePhotosRepository;
use App\Service\AwsImageService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        AwsImageService $awsImageService,
        StylePhotosRepository $stylePhotosRepository
    ) {
        $this->awsImageService = $awsImageService;
        $this->stylePhotosRepository = $stylePhotosRepository;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Utilisateurs')
            ->setDefaultSort(['id' => 'DESC'])
            ->setDefaultSort(['id' => 'DESC', 'lastname' => 'ASC', 'firstname' => 'DESC', 'createdAt' => 'DESC'])
            ->setPaginatorPageSize(30);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('lastname')
            ->add('firstname')
            ->add('username')
            ->add('email')
            ->add(BooleanFilter::new('isActive'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions

            ->add(Crud::PAGE_INDEX, Action::DETAIL)->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])->setPermission(Action::DELETE, 'ROLE_ADMIN')
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)->setPermission(Action::NEW, 'ROLE_ADMIN')

            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('mdi mdi-plus')->setLabel('Ajouter un utilisateur');
            })

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
        $fields = [
            IdField::new('id', 'ID')->hideOnForm(),
            ImageField::new('Avatar')->setBasePath($this->awsImageService->getPath())->hideOnForm()->setFormTypeOption('disabled', 'disabled')->addCssClass('table-user'),
            TextField::new('username', 'Pseudo'),
            TextField::new('lastname', 'Nom'),
            TextField::new('firstname', 'Prénom'),
            EmailField::new('email')->hideOnIndex(),
            AssociationField::new('profession', 'Profession'),
            AssociationField::new('experience', 'Expérience'),
            TextEditorField::new('about', 'À propos')->hideOnIndex(),
            DateField::new('created_at', 'Date de création'),
            DateField::new('updated_at', 'Date de mise à jour'),
            TextField::new('uuid', 'Uuid')->hideOnIndex(),
            BooleanField::new('isActive', 'Actif'),
            BooleanField::new('isDemo', 'Demo'),

            FormField::addPanel()->setHelp('BOOK')->hideOnForm(),
            AssociationField::new('book', 'Nom')->hideOnIndex()->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getBook()->getName();
            }),
            AssociationField::new('book', 'Autorise le seo')->hideOnIndex()->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getBook()->getAllowSeo() ? 'Oui' : 'Non';
            }),
            AssociationField::new('book', 'Description')->hideOnIndex()->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getBook()->getDescription();
            }),
            AssociationField::new('book', 'Mots-clés')->hideOnIndex()->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getBook()->getKeywords();
            }),
            AssociationField::new('book', 'Code analytics')->hideOnIndex()->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getBook()->getCodeAnalytics();
            }),
            AssociationField::new('book', 'Affiche la page contact')->hideOnIndex()->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getBook()->getShowContact() ? 'Oui' : 'Non';
            }),
            AssociationField::new('book', 'Autorise les commentaires')->hideOnIndex()->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getBook()->getAllowComments() ? 'Oui' : 'Non';
            }),
            AssociationField::new('book', 'Affiche le nombre de visites')->hideOnIndex()->hideOnForm()->formatValue(function ($value, $entity) {
                return $entity->getBook()->getShowVisitorCounter() ? 'Oui' : 'Non';
            }),
            AssociationField::new('book', 'Style de photos')->hideOnIndex()->hideOnForm()->formatValue(function ($value, $entity) {
                $stylePhoto = '';
                if ($entity->getBook()->getStylePhotos()) {
                    foreach ($entity->getBook()->getStylePhotos() as $i => $k) {
                        $name = $this->stylePhotosRepository->find($k);
                        if ($name) {
                            $stylePhoto .= $name->getTitle().', ';
                        }
                    }

                    return rtrim($stylePhoto, ', ');
                }
            }),

            FormField::addPanel()
                ->setHelp('Réseau sociaux')->hideOnForm(),
            AssociationField::new('social', 'Réseau sociaux')->hideOnIndex()->hideOnForm(),
        ];
        /*
        if ($pageName == Crud::PAGE_INDEX || $pageName == Crud::PAGE_DETAIL) {
            $fields[] = $image;
        } else {
            $fields[] = $imageFile;
        }*/

        return $fields;
    }
}
