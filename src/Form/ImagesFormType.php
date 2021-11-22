<?php

namespace App\Form;

use App\Entity\Images;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImagesFormType extends AbstractType
{
    private $em;
    private $security;

    /**
     * The Type requires the EntityManager as argument in the constructor. It is autowired
     * in Symfony 3.
     */
    public function __construct(EntityManagerInterface $em, Security $security, TranslatorInterface $translator, GalleryRepository $galleryRepository)
    {
        $this->em = $em;
        $this->security = $security;
        $this->translator = $translator;
        $this->galleryRepository = $galleryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder->add('title');
        //$form->add('isVisible');

        $imageConstraints = [
            /*new Image([
                'maxSize' => '5M'
            ]),*/
            new NotBlank([
                'message' => $this->translator->trans('Merci de sélectionner au moins un fichier.'),
            ]),
        ];

        $builder->add('imageFile', FileType::class, [
            'label_attr' => ['class' => 'd-none'],
            'label' => false,
            'help' => $this->translator->trans('Uniquement des fichiers JPG ou JPEG. 8Mo maximum par fichier.'),
            'multiple' => true,
            'mapped' => false,
            'required' => true,
            'constraints' => $imageConstraints,
        ]);

        $listGalleries = $this->galleryRepository->findBy(['user' => $this->security->getUser()]);
        $builder->add('gallery', EntityType::class, [
            'label' => $this->translator->trans('Dans quelle galerie souhaitez-vous ajouter des photos ?'),
            'attr' => [
                'class' => 'w-select',
            ],
            'placeholder' => $this->translator->trans('Sélectionner une galerie'),
            'constraints' => [
                new NotBlank(['message' => $this->translator->trans('Merci de sélectionner une galerie')]),
            ],
            'class' => 'App\Entity\Gallery',
            'choices' => $listGalleries,
        ]);

        $builder->add('isNSFW', CheckboxType::class, [
            'label' => $this->translator->trans('Contenu NSFW'),
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'help' => $this->translator->trans('Photo réservée à un public majeur. Contenu sexuellement explicite ou suggestif.'),
        ]);

        $builder->add('isHome', CheckboxType::class, [
            'label' => $this->translator->trans('Visible uniquement sur mon book'),
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'help' => $this->translator->trans("Elle n'apparaîtra pas dans le fil d'actualité de votre réseau."),
        ]);

        $builder->add('isGallery', CheckboxType::class, [
            'label' => $this->translator->trans('Visible uniquement depuis la galerie'),
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'help' => $this->translator->trans("Elle n'apparaîtra pas sur la page d'accueil de votre book."),
        ]);

        $builder->add('allowFavorites', CheckboxType::class, [
            'attr' => [
                'checked' => 'checked',
            ],
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'label' => $this->translator->trans('Autoriser la mise en avant dans les coups de cœur'),
            'help' => $this->translator->trans('Elle est susceptible de faire partie de nos coups de coeur.'),
        ]);

        $builder->add('allowLikes', CheckboxType::class, [
            'attr' => [
                'checked' => 'checked',
            ],
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'label' => $this->translator->trans('Autoriser les likes'),
            'help' => $this->translator->trans('Permet aux artistes (connectés) de liker cette photo.'),
        ]);

        $builder->add('allowComments', CheckboxType::class, [
            'attr' => [
                'checked' => 'checked',
            ],
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'label' => $this->translator->trans('Autoriser les commentaires'),
            'help' => $this->translator->trans('Permet aux artistes (connectés) de commenter cette photo.'),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Images::class,
        ]);
    }
}
