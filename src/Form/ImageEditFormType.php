<?php

namespace App\Form;

use App\Entity\Images;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImageEditFormType extends AbstractType
{
    private $em;
    private $security;

    /**
     * The Type requires the EntityManager as argument in the constructor. It is autowired
     * in Symfony 3.
     */
    public function __construct(EntityManagerInterface $em, Security $security, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->security = $security;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoExperience = $this->em->getRepository('App\Entity\Gallery');
        $choices = $repoExperience->createQueryBuilder('q')
            ->andWhere('q.user = :user')
            ->setParameter('user', $this->security->getUser())
            ->getQuery()
            ->getResult();

        $builder->add('title', TextType::class, [
            'help' => '',
        ]);

        $builder->add('description', TextareaType::class, [
            'help' => '',
            'attr' => [
                'class' => 'mt-2',
            ],
        ]);

        $builder->add('gallery', EntityType::class, [
            'label' => 'gallery',
            'attr' => [
                'class' => 'w-select',
            ],
            'placeholder' => '',
            'constraints' => [
                new NotBlank(['message' => 'Hein ?']),
            ],
            'class' => 'App\Entity\Gallery',
            'choices' => $choices,
        ]);

        $builder->add('copyright', TextType::class, [
            'help' => $this->translator->trans("Ex : Le nom de l'artiste à l'origine de cette photo."),
        ]);

        $builder->add('keywords', TextType::class, [
            'help' => $this->translator->trans('Saisissez vos mots clés. Ex : Portraits, Mode, Lifestyles'),
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
            'label' => $this->translator->trans('Autoriser la mise en avant dans les coups de cœur'),
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'help' => $this->translator->trans('Elle est susceptible de faire partie de nos coups de coeur.'),
        ]);

        $builder->add('allowLikes', CheckboxType::class, [
            'label' => $this->translator->trans('Autoriser les likes'),
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'help' => $this->translator->trans('Permet aux artistes (connectés) de liker cette photo'),
        ]);

        $builder->add('allowComments', CheckboxType::class, [
            'label' => $this->translator->trans('Autoriser les commentaires'),
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'help' => $this->translator->trans('Permet aux utilisateurs de commenter cette photo'),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Images::class,
        ]);
    }
}
