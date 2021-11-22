<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccountDeactivateTypeFormType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('currentPassword', PasswordType::class, [
            'label' => $this->translator->trans('Mot de passe actuel'),
            'required' => true,
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => $this->translator->trans('Merci de saisir votre mot de passe actuel'),
                ]),
            ],
        ]);

        $builder->add('deactivate', CheckboxType::class, [
            'mapped' => false,
            'required' => true,
            'label' => $this->translator->trans('Je confirme la désactivation de mon compte'),
            'constraints' => [
                new IsTrue([
                    'message' => $this->translator->trans('Veuillez cocher la case pour désactiver votre compte'),
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
