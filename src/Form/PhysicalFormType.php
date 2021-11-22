<?php

namespace App\Form;

use App\Entity\Physical;
use App\Repository\EthnicityRepository;
use App\Repository\EyesColorRepository;
use App\Repository\GenderListRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class PhysicalFormType extends AbstractType
{
    private $security;

    public function __construct(Security $security, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('birthday', DateType::class, [
            'required' => false,
            'widget' => 'single_text',
            'help' => $this->translator->trans('Confidentiel. Utilisé uniquement pour de la recherche avancée'),
        ]);

        $builder->add('ethnicity', EntityType::class, [
            'required' => false,
            'class' => 'App\Entity\Ethnicity',
            'query_builder' => function (EthnicityRepository $er) {
                return $er->createQueryBuilder('e')
                    ->andWhere('e.isActive = :isActive')
                    ->setParameter('isActive', true)
                    ->orderBy('e.id', 'ASC');
            },
        ]);

        $builder->add('hairColor', EntityType::class, [
            'required' => false,
            'class' => 'App\Entity\HairColor',
        ]);

        $builder->add('eyesColor', EntityType::class, [
            'required' => false,
            'class' => 'App\Entity\EyesColor',
            'query_builder' => function (EyesColorRepository $er) {
                return $er->createQueryBuilder('e')
                    ->andWhere('e.isActive = :isActive')
                    ->setParameter('isActive', true)
                    ->orderBy('e.id', 'ASC');
            },
        ]);

        $builder->add('gender', EntityType::class, [
            'required' => false,
            'class' => 'App\Entity\GenderList',
            'query_builder' => function (GenderListRepository $er) {
                return $er->createQueryBuilder('e')
                    ->orderBy('e.id', 'ASC');
            },
        ]);

        $user = $this->security->getUser();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user) {
            $form = $event->getForm();

            if ('modeles' == $user->getProfession()->getSlug()) {
                $form->add('size', NumberType::class, [
                    'required' => false,
                ])
                    ->add('weight', NumberType::class, [
                        'required' => false,
                    ])
                    ->add('hip', TextType::class, [
                        'required' => false,
                    ])
                    ->add('chest', NumberType::class, [
                        'required' => false,
                    ])
                    ->add('confection', TextType::class, [
                        'required' => false,
                    ])
                    ->add('pointure', TextType::class, [
                        'required' => false,
                    ])
                    ->add('chestSize', TextType::class, [
                        'required' => false,
                    ])
                    ->add('waistSize', TextType::class, [
                        'required' => false,
                    ])
                    ->add('chestHeight', TextType::class, [
                        'required' => false,
                    ])
                    ->add('heightBust', TextType::class, [
                        'required' => false,
                    ])
                    ->add('backHeight', TextType::class, [
                        'required' => false,
                    ])
                    ->add('shoulderWidth', TextType::class, [
                        'required' => false,
                    ])
                    ->add('armLength', TextType::class, [
                        'required' => false,
                    ])
                    ->add('armsTurn', TextType::class, [
                        'required' => false,
                    ])
                    ->add('roundNeck', TextType::class, [
                        'required' => false,
                    ]);
            }

            if ('photographes' == $user->getProfession()->getSlug()) {
                $form->add('apnCamera', NumberType::class, [])
                    ->add('apnLenses', NumberType::class, []);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user) {
            $form = $event->getForm();

            if ('photographe' == $user->getProfession()->getSlug()) {
                $form->add('apnCamera');
                $form->add('apnLenses', TextareaType::class, [
                    'help' => $this->translator->trans('Vous pouvez ajouter plusieurs objectifs'),
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Physical::class,
            'validation_groups' => 'profile',
        ]);
    }
}
