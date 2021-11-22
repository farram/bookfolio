<?php

namespace App\Form;

use App\Entity\Social;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('facebook', UrlType::class, [
                'required' => false,
            ])
            ->add('instagram', UrlType::class, [
                'required' => false,
            ])
            ->add('tumblr', UrlType::class, [
                'required' => false,
            ])
            ->add('twitter', UrlType::class, [
                'required' => false,
            ])
            ->add('pinterest', UrlType::class, [
                'required' => false,
            ])
            ->add('linkedin', UrlType::class, [
                'required' => false,
            ])
            ->add('skype', UrlType::class, [
                'required' => false,
            ])
            ->add('website', UrlType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Social::class,
        ]);
    }
}
