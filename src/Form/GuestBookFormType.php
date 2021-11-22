<?php

namespace App\Form;

use App\Entity\Guestbook;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class GuestBookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Author', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Qui êtes-vous ?',
                    ]),
                ],
            ])
            ->add('location', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'indiquer un lieu',
                    ]),
                ],
            ])
            ->add('route', HiddenType::class, [
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('locality', HiddenType::class, [
                'mapped' => false,
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('adminstrative_area', HiddenType::class, [
                'mapped' => false,
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('country', HiddenType::class, [
                'mapped' => false,
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('postalCode', HiddenType::class, [
                'mapped' => false,
                'attr' => [
                    'readonly' => true,
                ],
            ])

            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir votre adresse e-mail',
                    ]),
                ],
            ])
            ->add('website', UrlType::class)
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir votre commentaire',
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
            'data_class' => Guestbook::class,
        ]);
    }
}
