<?php

namespace App\Controller;

use App\Form\AccountDeactivateTypeFormType;
use App\Form\AddressFormType;
use App\Form\OptionFormType;
use App\Form\PhysicalFormType;
use App\Form\ProfileFormType;
use App\Form\SigninEmailEditTypeFormType;
use App\Form\SocialFormType;
use App\Form\UserChangePasswordFormType;
use App\Service\RandomFlashMessage;
use App\StripeClient;
use App\Subscription\SubscriptionHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/dashboard/account", name="account_")
 */
class AccountController extends AbstractController
{
    public function __construct(
        TranslatorInterface $translator,
        RandomFlashMessage $randomFlashMessage,
        Breadcrumbs $breadcrumbs
    ) {
        $this->translator = $translator;
        $this->randomFlashMessage = $randomFlashMessage;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Mes informations.
     *
     * @Route("/overview", name="overview")
     */
    public function AccountOverview(Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Aperçu');

        return $this->render('dashboard/account/overview.html.twig', ['title' => $this->translator->trans('Votre compte')]);
    }

    /**
     * Mes informations.
     *
     * @Route("/settings", name="settings")
     */
    public function AccountDetails(Breadcrumbs $breadcrumbs, Request $request, string $uploadDir): Response
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Paramètres');

        $user = $this->getUser();
        $profileForm = $this->createForm(ProfileFormType::class, $user, []);

        // Gestion du formulaire edition profil : User

        $profileForm->handleRequest($request);
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $user->setUpdatedAt(new DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Vos informations ont bien été sauvegardées'),
                ]);

            return $this->redirect($request->headers->get('referer') . '#informations');
        }

        /**
         * Gestion du formulaire edition profil : Adresse.
         */
        $addressForm = $this->createForm(AddressFormType::class, $user->getAddress());
        $addressForm->handleRequest($request);
        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $user->setUpdatedAt(new DateTime('now'));
            $address = $user->getAddress();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($address);
            $entityManager->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => 'Top !',
                    'message' => $this->translator->trans('Vos informations ont bien été sauvegardées'),
                ]);

            return $this->redirect($request->headers->get('referer') . '#address');
        }

        /**
         * Gestion du formulaire edition profil : Physique.
         */
        $physicalForm = $this->createForm(PhysicalFormType::class, $user->getPhysical());
        $physicalForm->handleRequest($request);
        if ($physicalForm->isSubmitted() && $physicalForm->isValid()) {
            //On met à jour la date de mise à jour
            $user->setUpdatedAt(new DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => 'Top !',
                    'message' => $this->translator->trans('Vos informations ont bien été sauvegardées'),
                ]);

            return $this->redirect($request->headers->get('referer') . '#physique');
        }

        //Gestion du formulaire edition profil : Social

        $socialForm = $this->createForm(SocialFormType::class, $user->getSocial());
        $socialForm->handleRequest($request);
        if ($socialForm->isSubmitted() && $socialForm->isValid()) {
            $user->setUpdatedAt(new DateTime('now'));
            $social = $user->getSocial();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($social);
            $entityManager->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Vos informations ont bien été sauvegardées'),
                ]);

            return $this->redirect($request->headers->get('referer') . '#social');
        }

        return $this->render('dashboard/account/settings.html.twig', [
            'profileForm' => $profileForm->createView(),
            'addressForm' => $addressForm->createView(),
            'physicalForm' => $physicalForm->createView(),
            'socialForm' => $socialForm->createView(),
            'title' => $this->translator->trans('Paramètres du compte'),
        ]);
    }

    /**
     * Sécurité.
     *
     * @Route("/security", name="security")
     */
    public function AccountSecurity(Breadcrumbs $breadcrumbs, Request $request, UserPasswordHasherInterface $passwordEncoder)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Sécurité');

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $signinEmailEditTypeForm = $this->createForm(SigninEmailEditTypeFormType::class, $user);
        $signinEmailEditTypeForm->handleRequest($request);
        if ($signinEmailEditTypeForm->isSubmitted() && $signinEmailEditTypeForm->isValid()) {
            $currentPassword = $signinEmailEditTypeForm->get('currentPassword')->getData();
            if ($passwordEncoder->isPasswordValid($user, $currentPassword)) {
                $user->setEmail($signinEmailEditTypeForm->get('email')->getData());
                $user->setUpdatedAt(new DateTime('now'));
                $em->persist($user);
                $em->flush();
                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->randomFlashMessage->getTitle(),
                        'message' => $this->translator->trans('Votre nouvelle adresse e-mail a bien enregistrée.'),
                    ]);
            } else {
                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'error',
                        'title' => 'Désolé...',
                        'message' => $this->translator->trans('Le mot de passe saisi ne correspond pas à votre mot de passe actuel.'),
                    ]);
            }
        }

        $accountDeactivateTypeForm = $this->createForm(AccountDeactivateTypeFormType::class, $user);

        $form = $this->createForm(UserChangePasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();

            /*
             * Si l'ancien mot de passe est bon
             */

            if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                $newEncodedPassword = $passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData());

                $user->setPassword($newEncodedPassword);
                $user->setUpdatedAt(new DateTime('now'));
                $em->persist($user);
                $em->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->randomFlashMessage->getTitle(),
                        'message' => $this->translator->trans('Votre mot de passe à bien été changé !'),
                    ]);
            } else {
                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'error',
                        'title' => 'Zut !',
                        'message' => $this->translator->trans('Votre ancien mot de passe est incorrect.'),
                    ]);
            }

            return $this->redirect($request->headers->get('referer'));
        }

        $form_deactivate = $this->createForm(AccountDeactivateTypeFormType::class);
        $form_deactivate->handleRequest($request);
        if ($form_deactivate->isSubmitted() && $form_deactivate->isValid()) {
            $password = $form_deactivate->get('currentPassword')->getData();
            if ($passwordEncoder->isPasswordValid($this->getUser(), $password)) {
                $this->get('security.token_storage')->setToken(null);
                $em->remove($this->getUser());
                $em->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->translator->trans('Au revoir !'),
                        'message' => $this->translator->trans('Votre compte a bien été définitivement supprimé.'),
                    ]);

                return $this->redirectToRoute('home');
            } else {
                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'error',
                        'title' => 'Zut !',
                        'message' => $this->translator->trans('Votre mot de passe est incorrect. Vous devez fournir un mot de passe valide pour désactiver votre compte.'),
                    ]);

                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->render('dashboard/account/security.html.twig', [
            'form' => $form->createView(),
            'signinEmailEditTypeForm' => $signinEmailEditTypeForm->createView(),
            'accountDeactivateTypeForm' => $accountDeactivateTypeForm->createView(),
            'title' => $this->translator->trans('Sécurité du compte'),
        ]);
    }

    /**
     * Notifications.
     *
     * @Route("/notifications", name="notifications")
     */
    public function AccountNotifications(Breadcrumbs $breadcrumbs, Request $request)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Notifications mail');

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form = $this->createForm(OptionFormType::class, $user->getOption());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new DateTime('now'));
            $option = $user->getOption();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($option);
            $entityManager->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Vos informations ont bien été sauvegardées'),
                ]);

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('dashboard/account/notifications.html.twig', ['form' => $form->createView(), 'title' => $this->translator->trans('Vos notifications')]);
    }

    /**
     * Abonnement.
     *
     * @Route("/subscription", name="subscription")
     */
    public function AccountSubscription(Breadcrumbs $breadcrumbs, Request $request, SubscriptionHelper $subscriptionHelper, StripeClient $stripeClient)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans('Abonnement'));

        $currentPlan = null;
        $otherPlan = null;
        $otherDurationPlan = null;
        if ($this->getUser()->hasActiveSubscription()) {
            $currentPlan = $subscriptionHelper->findPlan($this->getUser()->getSubscription()->getStripePlanId());
            $otherPlan = $subscriptionHelper->findPlanToChangeTo($currentPlan->getPlanId());
            $otherDurationPlan = $subscriptionHelper->findPlanForOtherDuration($currentPlan->getPlanId());
        }

        $invoices = $stripeClient->findPaidInvoices($this->getUser());

        $stripe = new \Stripe\StripeClient(
            $this->getParameter('stripe_secret_key')
        );
        // $plan = $stripe->plans->retrieve(
        //     $currentPlan->getPlanId(),
        //     []
        // );

        return $this->render('dashboard/account/subscription.html.twig', [
            'error' => null,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'currentPlan' => $currentPlan,
            'otherPlan' => $otherPlan,
            'otherDurationPlan' => $otherDurationPlan,
            'invoices' => $invoices,
            'title' => $this->translator->trans('Votre abonnement'),
        ]);
    }

    /**
     * Suppression du compte.
     *
     * @Route("/delete", name="delete")
     */
    public function AccountDelete(PasswordHasherFactoryInterface $passwordHasherFactoryInterface, Request $request, EntityManagerInterface $em): Response
    {
        $form_deactivate = $this->createForm(AccountDeactivateTypeFormType::class);
        $form_deactivate->handleRequest($request);

        return $this->redirectToRoute('account_security');
    }
}
