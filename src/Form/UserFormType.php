<?php

namespace App\Form;

use App\Entity\Profession;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserFormType extends AbstractType
{
    private $em;

    /**
     * The Type requires the EntityManager as argument in the constructor. It is autowired
     * in Symfony 3.
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    protected function addElements(FormInterface $form, Profession $profession = null)
    {
        $form->add('profession', EntityType::class, [
            'data' => $profession,
            'attr' => ['class' => 'w-select'],
            'class' => 'App\Entity\Profession',
        ]);

        $experience = [];
        if ($profession) {
            $repoExperience = $this->em->getRepository('App\Entity\Experience');
            $experience = $repoExperience->createQueryBuilder('q')
                ->where('q.profession = :professionId')
                ->setParameter('professionId', $profession->getId())
                ->getQuery()
                ->getResult();
        }

        $form->add('experience', EntityType::class, [
            'label' => $this->translator->trans('Mon expérience'),
            'attr' => [
                'class' => 'w-select',
            ],
            'placeholder' => '',
            'class' => 'App\Entity\Experience',
            'choices' => $experience,
        ]);

        $form->add('firstname', TextType::class, [
            'empty_data' => '',
            'required' => true,
        ]);

        $form->add('lastname', TextType::class, [
            'empty_data' => '',
            'required' => true,
        ]);

        $form->add('email', TextType::class, [
            'empty_data' => '',
        ]);

        $form->add('about', CKEditorType::class, [
            'attr' => ['class' => 'summernote'],
            'help' => $this->translator->trans('La biographie s\'affichera dans la page "À propos" de votre book et dans la page d\'accueil en fonction du template que vous aurez choisi.'),
        ]);
    }

    public function onPreSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        // When you create a new person, the Profession is always empty
        $profession = $user->getProfession() ? $user->getProfession() : null;

        $this->addElements($form, $profession);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        // Search for selected City and convert it into an Entity
        $profession = $this->em->getRepository('App\Entity\Profession')->find($data['profession']);

        $this->addElements($form, $profession);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'error_bubbling' => false,
        ]);
    }
}
