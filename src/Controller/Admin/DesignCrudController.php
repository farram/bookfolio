<?php

namespace App\Controller\Admin;

use App\Entity\Design;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class DesignCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Design::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $imageFile = ImageField::new('imageFile', 'Cover')->setFormType(VichImageType::class);
        $image = ImageField::new('image', 'Cover')->setBasePath('assets/img/themes');

        $fields = [
            //IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
            BooleanField::new('isActive', 'Actif'),
            BooleanField::new('isCustom', 'Custom'),
            AssociationField::new('plan', 'Feature'),
        ];

        if (Crud::PAGE_INDEX == $pageName || Crud::PAGE_DETAIL == $pageName) {
            $fields[] = $image;
        } else {
            // $fields[] = $imageFile;
        }

        return $fields;
    }
}
