<?php

namespace App\Form;

use App\Entity\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChangePasswordFormType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('oldPassword', PasswordType::class, [
                'label' => $this->translator->trans('Mot de passe actuel'),
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Merci de saisir votre mot de passe actuel'),
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => false,
                'attr' => [
                    'class' => 'text-dark',
                ],
                'first_options' => [
                    'constraints' => [
                        new Regex(['pattern' => '/^(?=.*[a-z])(?=.*\\d).{5,}$/i', 'message' => 'Le nouveau mot de passe doit comporter au moins 5 caractères et inclure au moins une lettre et un chiffre.']),
                        new NotBlank([
                            'message' => $this->translator->trans('Merci de définir votre nouveau mot de passe'),
                        ]),
                        new Length([
                            'min' => 5,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 16,
                            'maxMessage' => 'Votre mot de passe ne peut pas contenir plus de {{ limit }} caractères',
                        ]),
                    ],
                    'label' => $this->translator->trans('Nouveau mot de passe'),
                    'help' => $this->translator->trans('7 à 16 caractères. Au moins une lettre et un chiffre.'),
                ],
                'second_options' => [
                    'label' => $this->translator->trans('Confirmez le nouveau mot de passe'),
                ],
                'invalid_message' => $this->translator->trans('Les deux mots de passe doivent être identiques'),
                'mapped' => false,
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'validation_groups' => 'reset_password',
            //'data_class' => ChangePassword::class,
        ]);
    }
}
