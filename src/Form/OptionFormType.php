<?php

namespace App\Form;

use App\Entity\Option;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contactPublishMedias', CheckboxType::class, [
            'label' => 'Publication de mes contacts',
            'help' => 'Recevoir une notification lorsqu\'un de mes contacts publie une nouvelle photo.',
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'required' => false,
        ]);

        $builder->add('contactShareMessage', CheckboxType::class, [
            'label' => 'Mentions',
            'help' => 'Recevoir une notification lorsque quelqu\'un mentionne mon nom.',
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'required' => false,
        ]);

        $builder->add('contactSendPrivateMessage', CheckboxType::class, [
            'label' => 'Message privé',
            'help' => 'Recevoir une notification lorsque vous recevrez un nouveau message privé.',
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'required' => false,
        ]);

        $builder->add('commentImage', CheckboxType::class, [
            'label' => 'Commentaire photos',
            'help' => 'Recevoir une notification lorsque l\'on commente une de mes photos.',
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'required' => false,
        ]);

        $builder->add('follow', CheckboxType::class, [
            'label' => 'Abonnement (follow)',
            'help' => 'Recevoir une notification lorsqu\'un utilisateur me suit.',
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Option::class,
        ]);
    }
}
