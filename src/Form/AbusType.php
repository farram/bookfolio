<?php

namespace App\Form;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AbusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('book', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Indiquez votre book',
                ]),
            ],
        ]);

        $builder->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Saisissez votre adresse e-mail',
                ]),
            ],
        ]);

        $builder->add('type', ChoiceType::class, [
            'label' => 'Que signalez-vous ?',
            'choices' => [
                'Vol de photos' => 1,
                'Contenu pornographique' => 2,
                'Liens vers des sites pornographiques' => 3,
                'Violation de copyright' => 4,
                'Usurpation d\'identité' => 5,
                'Haine, racisme ou insultes' => 6,
                'Spams, fakes ou arnaques' => 7,
                'Ventes illicites' => 8,
                'Commentaires ou messages indésirables' => 9,
                'Supprimer son compte' => 10,
            ],
        ]);

        $builder->add('message', TextareaType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Saisissez votre message',
                ]),
            ],
        ]);

        $builder->add('recaptcha', EWZRecaptchaType::class, [
            'mapped' => false,
            'attr' => [
                'options' => [
                    'theme' => 'light',
                    'type' => 'image',
                    'size' => 'normal',
                ],
            ],
            'constraints' => [
                new RecaptchaTrue(),
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
