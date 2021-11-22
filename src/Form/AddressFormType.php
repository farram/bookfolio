<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddressFormType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fullAddress', null, [
            'required' => true,
            'help' => $this->translator->trans('Commencez à saisir votre ville, puis sélectionnez parmi les propositions qui apparaîtront.'),
            'label' => $this->translator->trans('Dans quelle ville habitez-vous ?'),
            'attr' => [
                'onFocus' => 'geolocate()',
                'class' => 'text-field bottom-margin-field w-input',
            ],
        ]);

        $builder->add('route', TextType::class, [
            'required' => false,
            'attr' => [
                'readonly' => true,
            ],
        ]);
        $builder->add('locality', TextType::class, [
            'required' => false,
            'attr' => [
                'readonly' => true,
            ],
        ]);
        $builder->add('adminstrative_area', TextType::class, [
            'required' => false,
            'attr' => [
                'readonly' => true,
            ],
        ]);
        $builder->add('country', TextType::class, [
            'required' => false,
            'attr' => [
                'readonly' => true,
            ],
        ]);
        $builder->add('postalCode', TextType::class, [
            'required' => false,
            'attr' => [
                'readonly' => true,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'validation_groups' => 'profile',
        ]);
    }
}
