<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\StylePhotos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\Regex;

class BookFormType extends AbstractType
{
    private $em;

    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, [
            'constraints' => [
                new Regex(['pattern' => '/^[A-Z]{5,}$/i', 'message' => 'Uniquement des lettres']),
                new NotEqualTo(['value' => 'admin', 'message' => 'Vous ne pouvez pas choisir admin comme URL']),
                new NotEqualTo(['value' => 'administrateur', 'message' => 'Vous ne pouvez pas choisir administrateur comme URL']),
                new NotEqualTo(['value' => 'debug', 'message' => 'Vous ne pouvez pas choisir debug comme URL']),
                new NotEqualTo(['value' => 'testtest', 'message' => 'Vous ne pouvez pas choisir testtest comme URL']),
                new NotEqualTo(['value' => 'dev', 'message' => 'Vous ne pouvez pas choisir dev comme URL']),
                new NotEqualTo(['value' => 'prod', 'message' => 'Vous ne pouvez pas choisir prod comme URL']),
                new NotEqualTo(['value' => 'preprod', 'message' => 'Vous ne pouvez pas choisir preprod comme URL']),
                new NotBlank(['message' => 'Votre book sera consultable via ce lien']),
                new Length(
                    ['min' => 5, 'minMessage' => 'Votre url doit contenir au moins {{ limit }} caractères', 'max' => 30, 'maxMessage' => 'Votre url ne peut pas contenir plus de {{ limit }} caractères']
                ),
            ],
            'help' => '5 à 30 caractères. Pas de caractères spéciaux. Les changements seront prises en compte immédiatement.',
            'required' => true,
        ]);

        $repoExperience = $this->em->getRepository('App\Entity\StylePhotos');
        $styles = $repoExperience->createQueryBuilder('q')->getQuery()->getResult();

        $user = $this->security->getUser();

        $repoBook = $this->em->getRepository('App\Entity\Book');
        $book = $repoBook->createQueryBuilder('a')->andWhere('a.user = :val')->setParameter('val', $user->getId())->getQuery()->getResult();

        $list = [];
        foreach ($book as $choice) {
            $list[] = $choice->getListStylePhotos();
        }
        $choices_users = [];

        foreach ($list as $values) {
            if ($values) {
                $values_exploded = explode(',', $values);

                foreach ($values_exploded as $value) {
                    $repository = $this->em->getRepository(StylePhotos::class);
                    $st = $repository->find($value);

                    $choices_users[$value] = $st;
                }
            }
        }

        $builder->add('stylePhotos', ChoiceType::class, [
            'expanded' => false,
            'multiple' => true,
            'choices' => $styles,
            'data' => $choices_users,
            'choice_label' => function (?StylePhotos $stylePhotos) {
                return $stylePhotos ? ($stylePhotos->getTitle()) : '';
            },
            'help' => 'Choisissez le style de photos que vous faites. Vous pouvez en choisir plusieurs !',
        ]);

        $builder->add('showContact', CheckboxType::class, [
            'label' => 'Activer le formulaire de contact',
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'help' => 'Vous pouvez donner la possibilité aux personnes de vous contacter.',
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
        ]);

        $builder->add('allowComments', CheckboxType::class, [
            'label' => 'Activer les commentaires',
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'help' => 'Vos visiteurs peuvent donner leur avis sur votre travail.',
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
        ]);

        $builder->add('showVisitorCounter', null, [
            'label' => 'Afficher le compteur de visites',
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'help' => 'Affiche le nombre de visites que vous avez eu depuis la création de votre book.',
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
        ]);

        $builder->add('title', null, [
            'help' => '65 caractères au maximum',
            'constraints' => [
                new Length(['max' => 65, 'maxMessage' => 'Votre titre ne doit pas dépasser {{ limit }} caractères']),
            ],
        ]);
        $builder->add('allowSeo', null, [
            'label' => 'Autoriser le référencement',
            'label_attr' => [
                'class' => 'fw-bolder fs-5 mb-0',
            ],
            'help' => 'test test test',
            'help_attr' => [
                'class' => 'mt-0 px-10 text-muted fs-6',
            ],
        ]);
        $builder->add('description', null, [
            'constraints' => [
                new Length(
                    [
                        'max' => 150,
                        'maxMessage' => 'Votre description ne doit pas dépasser {{ limit }} caractères',
                    ]
                ),
            ],
            'help' => 'Entrez une brève description de votre book. Ce texte sera utilisé pour aider les moteurs de recherche à décrire votre book dans les résultats de recherche. 150 caractères au maximum.',
        ]);
        $builder->add('keywords', null, [
            'help' => 'Entrez une liste de mots-clés séparés par des virgules qui décrivent votre travail. Ces mots clés seront utilisés pour aider les moteurs de recherche à indexer votre book.',
        ]);

        $builder->add('codeAnalytics', null, [
            'help' => '',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
