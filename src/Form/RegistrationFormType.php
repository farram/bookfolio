<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Profession;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Repository\ExperienceRepository;
use App\Service\RouteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    private $em;

    /**
     * The Type requires the EntityManager as argument in the constructor. It is autowired
     * in Symfony 3.
     */
    public function __construct(EntityManagerInterface $em, ExperienceRepository $experienceRepository, RouteService $routeService)
    {
        $this->em = $em;
        $this->experienceRepository = $experienceRepository;
        $this->routeService = $routeService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    protected function addElements(FormInterface $form, Profession $profession = null)
    {
        $form->add('profession', EntityType::class, [
            'label' => false,
            'multiple' => false,
            'help' => 'Ex : Photographe, modèle, etc.',
            'data' => $profession,
            'attr' => ['class' => ''],
            'placeholder' => 'Vous êtes ',
            'class' => 'App\Entity\Profession',
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de saisir votre profession',
                ]),
            ],
        ]);
        $experience = [];

        if ($profession) {
            $experience = $this->experienceRepository->findBy(['profession' => $profession->getId()]);
        }

        $form->add('experience', EntityType::class, [
            'label' => false,
            'placeholder' => 'Votre expérience',
            'class' => 'App\Entity\Experience',
            'choices' => $experience,
            'constraints' => [
                new NotBlank([
                    'message' => 'Quelle expérience avez-vous ?',
                ]),
            ],
        ]);

        $form->add('email', EmailType::class, [
            'label' => 'Adresse e-mail',
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de saisir votre email',
                ]),
            ],
        ]);

        $form->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'label' => 'Mot de passe',
            'help' => '8 caractères min.',
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de saisir votre mot de passe',
                ]),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    'max' => 4096,
                ]),
            ],
        ]);

        $form->add('termsAccepted', CheckboxType::class, [
            'mapped' => false,
            'label' => 'Je certifie être majeur(e) et avoir lu et accepté <a href="' . $this->routeService->routeTerms() . '">les conditions d\'utilisation</a> ainsi que <a href="' . $this->routeService->routePoliticy() . '">la politique de confidentialité</a> de Book-folio.fr',
            'label_html' => true,
            'constraints' => [
                new IsTrue([
                    'message' => 'Eh oui... vous devez accepter nos conditions pour poursuivre.',
                ]),
            ],
        ]);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $user = $event->getData();
        $form = $event->getForm();

        $profession = $user->getProfession() ? $user->getProfession() : null;

        $this->addElements($form, $profession);
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $user = $event->getData();

        if (!$user) {
            return;
        }

        $profession = $this->em->getRepository('App\Entity\Profession')->find($user['profession']);

        $this->addElements($form, $profession);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            //'csrf_protection' => false,
        ]);
    }
}
