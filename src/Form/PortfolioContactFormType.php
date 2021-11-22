<?php

namespace App\Form;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PortfolioContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Qui êtes-vous ?',
                ]),
            ],
        ]);
        $builder->add('society');
        $builder->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Une adresse email pour vous recontacter ?',
                ]),
            ],
        ]);
        $builder->add('phone');
        $builder->add('message', TextareaType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Quel est votre message ?',
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
