<?php

namespace App\Form;

use App\Entity\Images;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UploadFastImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder->add('title');
        //$form->add('isVisible');

        $imageConstraints = [new NotBlank([
            'message' => 'EUhhhhhh',
            ]),
        ];

        $builder->add('imageFile', FileType::class, [
            'label_attr' => ['class' => 'd-none'],
            'label' => false,
            'help' => 'Uniquement des fichiers JPG ou JPEG. 8Mo maximum par fichier',
            'multiple' => true,
            'mapped' => false,
            'required' => true,
            'constraints' => $imageConstraints,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Images::class,
            ]);
    }
}
