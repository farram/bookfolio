<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationStepTwoFormType extends AbstractType
{
    // protected function addElements(FormInterface $form, User $user = null)
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', FileType::class, [
                'required' => true,
                'label' => 'Photo principale de votre book',
                'help' => 'Modifiable à tout moment.',
                'constraints' => [
                    new NotBlank(['message' => 'Sélectionner votre photo de profil']),
                ],
                'attr' => [
                    'label' => false,
                    'class' => null,
                    'data-fileuploader-default' => 'https://innostudio.de/fileuploader/images/default-avatar.png',
                    'data-fileuploader-files' => '',
                ],
            ]);

        $builder->add('lastname', TextType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'Saisissez votre nom']),
            ],
            'label' => 'Votre nom',
            'attr' => [
                'class' => 'text-field bottom-margin-field w-input',
            ],
        ]);
        $builder->add('firstname', TextType::class, [
            'label' => 'Votre prénom',
            'constraints' => [
                new NotBlank(['message' => 'Saisissez votre prénom']),
            ],
            'required' => true,
            'attr' => [
                'class' => 'text-field bottom-margin-field w-input',
            ],
        ]);

        $builder->add('fullAdresse', TextType::class, [
            'mapped' => false,
            'constraints' => [
                new NotBlank(['message' => 'Saisissez votre ville']),
            ],
            'required' => true,
            'help' => 'Commencez à saisir votre ville, puis sélectionnez parmi les propositions qui apparaîtront.',
            'label' => 'Dans quelle ville habitez-vous ?',
            'attr' => [
                'onFocus' => 'geolocate()',
                'class' => 'text-field bottom-margin-field w-input',
            ],
        ]);
        $builder->add('route', HiddenType::class, [
            'label' => false,
            'attr' => [
                'hidden' => true,
            ],
        ]);
        $builder->add('locality', HiddenType::class, [
            'mapped' => false,
            'label' => false,
            'attr' => [
                'hidden' => true,
            ],
        ]);
        $builder->add('adminstrativeArea', HiddenType::class, [
            'label' => false,
            'attr' => [
                'hidden' => true,
            ],
        ]);
        $builder->add('country', HiddenType::class, [
            'label' => false,
            'attr' => [
                'hidden' => true,
            ],
        ]);
        $builder->add('postalCode', HiddenType::class, [
            'label' => false,
            'attr' => [
                'hidden' => true,
            ],
        ]);

        $builder->add('username', TextType::class, [
            'label' => 'Adresse de votre book',
            'constraints' => [
                new Regex(['pattern' => '/^[A-Z]{5,}$/i', 'message' => 'Uniquement des lettres']),
                new NotEqualTo(['value' => 'admin', 'message' => 'Vous ne pouvez pas choisir admin comme URL']),
                new NotEqualTo(['value' => 'administrateur', 'message' => 'Vous ne pouvez pas choisir administrateur comme URL']),
                new NotEqualTo(['value' => 'debug', 'message' => 'Vous ne pouvez pas choisir debug comme URL']),
                new NotEqualTo(['value' => 'testtest', 'message' => 'Vous ne pouvez pas choisir testtest comme URL']),
                new NotEqualTo(['value' => 'dev', 'message' => 'Vous ne pouvez pas choisir dev comme URL']),
                new NotEqualTo(['value' => 'prod', 'message' => 'Vous ne pouvez pas choisir prod comme URL']),
                new NotEqualTo(['value' => 'preprod', 'message' => 'Vous ne pouvez pas choisir preprod comme URL']),
                new NotBlank(['message' => 'Votre book sera consultable via ce lien']),
                new Length(
                    ['min' => 5, 'minMessage' => 'Votre url doit contenir au moins {{ limit }} caractères', 'max' => 30, 'maxMessage' => 'Votre url ne peut pas contenir plus de {{ limit }} caractères']
                ),
            ],
            'help' => 'Minimum 5 caractères, sans caractères spéciaux.',
            'attr' => [
                'placeholder' => 'Ex : julien',
            ],
            'required' => true,
        ]);
    }

    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults([
    //         'data_class' => User::class,
    //         'csrf_protection' => true,
    //         'csrf_field_name' => '_token',
    //         'cascade_validation' => true,
    //     ]);
    // }
}
