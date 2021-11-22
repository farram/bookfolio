<?php

namespace App\Form;

use App\Entity\Page;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PageEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'help' => 'Egalement utilisé comme URL définitive de la page.',
            'constraints' => [
                new NotBlank(['message' => 'Merci de saisir un titre']),
            ],
        ]);

        $builder->add('content', CKEditorType::class, [
            'attr' => ['class' => 'summernote'],
            'help' => 'Le contenu de votre page.',
        ]);

        $builder->add('isActive', CheckboxType::class, [
            'label' => 'Visible sur mon book',
            'help' => 'Cocher cette case pour rendre votre page visible sur votre book.',
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
