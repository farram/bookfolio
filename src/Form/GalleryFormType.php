<?php

namespace App\Form;

use App\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class GalleryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, [
            'constraints' => [
                new Regex(['pattern' => '/^[A-Za-z0-9-éèàêï\s]{2,}$/i', 'message' => 'Uniquement des chiffres ou des lettres.']),
                new NotBlank(['message' => 'Quel titre souhaitez-vous donner à votre galerie ?']),
                new Length([
                    'min' => 2,
                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                ]),
            ],
            'help' => 'Minimum 2 caractères. Pas de caractères spéciaux.',
            'required' => true,
            'attr' => ['autocomplete' => 'new-password', 'readonly' => 'readonly', 'onfocus' => "this.removeAttribute('readonly');"],
        ]);

        $builder
            ->add('description')
            ->add('isActive', CheckboxType::class, [
                'attr' => ['checked' => 'checked'],
                'label' => 'Afficher la galerie sur votre book',
            ])

            ->add('isProtect', CheckboxType::class, [
                'required' => false,
                'attr' => ['onchange' => 'displayPassword()'],
                'label' => 'Protéger la galerie par un mot de passe',
            ])

            ->add('passwordHash', PasswordType::class, [
                'help' => '7 à 16 caractères. Doit comporter au moins une lettre et un chiffre.',
                'always_empty' => true,
                'required' => false,
                'attr' => ['autocomplete' => 'new-password'],
                // 'attr' => array('style' => 'display:none;'),
                'constraints' => [
                    new Regex(['pattern' => '/^(?=.*[a-z])(?=.*\\d).{5,}$/i', 'message' => 'Le nouveau mot de passe doit comporter au moins 5 caractères et inclure au moins une lettre et un chiffre.']),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 16,
                        'maxMessage' => 'Votre mot de passe ne peut pas contenir plus de {{ limit }} caractères',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
}
