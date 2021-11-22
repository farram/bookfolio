<?php

namespace App\Form;

use App\Entity\Annonces;
use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnnonceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $form, array $options)
    {
        $form->add('profession', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Cela nous permettra de mieux orienter votre annonce.',
                ]),
            ],
        ]);

        $form->add('gender', EntityType::class, [
            'class' => 'App\Entity\GenderList',
        ]);

        /*$form->add('gender', ChoiceType::class, [
            'choices'  => User::GENDER,
            'constraints' => [
                new NotBlank([
                    'message' => 'Obligatoire',
                ]),
            ],
        ]);*/

        $form->add('type', ChoiceType::class, [
            'choices' => Annonces::TYPE,
            'constraints' => [
                new NotBlank([
                    'message' => 'Obligatoire',
                ]),
            ],
        ]);

        $form->add('location', null, [
            'help' => 'Commencez à saisir votre ville, puis sélectionnez parmi les propositions qui apparaîtront.',
            'attr' => [
                'onFocus' => 'geolocate()',
                'class' => 'text-field bottom-margin-field w-input',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Quelle serait éventuellement la localisation de la personne à qui s\'adresse votre annonce ?',
                ]),
            ],
        ]);

        $form->add('route', HiddenType::class, [
            'label' => false,
            'mapped' => false,
            'attr' => [
                'hidden' => true,
            ],
        ]);

        $form->add('locality', HiddenType::class, [
            'mapped' => false,
            'label' => false,
            'attr' => [
                'hidden' => true,
            ],
        ]);
        $form->add('adminstrativeArea', HiddenType::class, [
            'label' => false,
            'mapped' => false,
            'attr' => [
                'hidden' => true,
            ],
        ]);
        $form->add('country', HiddenType::class, [
            'label' => false,
            'mapped' => false,
            'attr' => [
                'hidden' => true,
            ],
        ]);
        $form->add('postalCode', HiddenType::class, [
            'label' => false,
            'mapped' => false,
            'attr' => [
                'hidden' => true,
            ],
        ]);

        $form->add('title', TextType::class, [
            'help' => 'Ex: Recherche photographe IDF',
            'constraints' => [
                new NotBlank([
                    'message' => 'C\'est tout de même la clé de votre annonce !',
                ]),
            ],
        ]);

        $form->add('description', CKEditorType::class, [
            'attr' => ['class' => 'summernote'],
            'help' => 'Décrivez le plus clairement possible votre annonce',
            'constraints' => [
                new NotBlank([
                    'message' => 'Racontez un petit peu ce que vous attendez de votre projet.',
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}
