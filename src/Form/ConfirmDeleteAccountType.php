<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfirmDeleteAccountType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('oldPassword', PasswordType::class, [
            'label' => $this->translator->trans('Mot de passe actuel'),
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => $this->translator->trans('Merci de saisir votre mot de passe actuel'),
                ]),
            ],
        ]);

        $builder->add('reason', TextareaType::class, [
            'label' => $this->translator->trans('Pourquoi nous quittez vous ?'),
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => $this->translator->trans('N\'ayez pas peur ! Vous n\'allez pas nous vexer :)'),
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
