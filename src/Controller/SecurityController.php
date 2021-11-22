<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Book;
use App\Entity\Option;
use App\Entity\Physical;
use App\Entity\Social;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RegistrationStepTwoFormType;
use App\Repository\DesignRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use App\Service\UploaderHelper;
use DateTime;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @param Request      $request
     * @param string       $uploadDir
     * @param FileUploader $uploader
     *
     * @return Response
     */
    private $emailVerifier;

    /**
     * On défini les valeurs max des uploads des avatars.
     */
    private const WIDTH = 300;
    private const HEIGHT = 300;

    private $imagine;

    public function __construct(EmailVerifier $emailVerifier, TranslatorInterface $translator)
    {
        $this->emailVerifier = $emailVerifier;
        $this->imagine = new Imagine();
        $this->translator = $translator;
    }

    /**
     * @Route("/connect/facebook", name="facebook_connect")
     */
    public function connect(ClientRegistry $clientRegistry)
    {
        //RedirectResponse
        // $client = $clientRegistry->getClient('facebook_main');
        // return $client->redirect(["https://www.googleapis.com/auth/userinfo.email"]);
    }

    /**
     * @Route("/connect/google", name="google_connect")
     */

    public function connectGoogle(ClientRegistry $clientRegistry): RedirectResponse
    {
        $client = $clientRegistry->getClient('google_main');
        return $client->redirect([
            'https://www.googleapis.com/auth/userinfo.email'
        ]);
    }



    /**
     * Génération du formulaire d'inscription : Étape 1.
     *
     * @Route("/signup", name="app_register")
     */
    public function register(MailerInterface $mailer, Request $request, PasswordHasherFactoryInterface $passwordHasherFactoryInterface): Response
    {
        /*
         * Si l'utilisateur est déjà connecté
         * on le redirige sur la page d'accueil
         */
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('home');
        } else {
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // 3) On génère un token aléatoire avec la fonction un peu plus bas
                $accessToken = $this->generateToken();

                // 5) Si le mail est bien parti, on sauvegarde les informations dans la BDD

                if ($mailer) {
                    $uuid = Uuid::uuid4();

                    // 6) On encode de mot de passe

                    //$password = $passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData());

                    $user->setPassword(
                        $passwordHasherFactoryInterface->getPasswordHasher(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );

                    try {

                        $user->setEmail($form->get('email')->getData());
                        $user->setUuid($uuid);
                        $user->setUsername(substr($form->get('email')->getData(), 0, strpos($form->get('email')->getData(), '@')));
                        $user->setLastname('');
                        $user->setFirstname('');
                        $user->setThumbnail('');
                        $user->setRoles(['ROLE_USER']);
                        $user->setAccessToken($accessToken);
                        $user->setCreatedAt(new \DateTime('now'));
                        $user->setUpdatedAt(new \DateTime('now'));
                        $user->setIsActive(false);
                        $user->setIsVerified(false);

                        // 7) On sauvegarde le User!

                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($user);
                        $entityManager->flush();

                        // 4) On envoi un mail à l'utilisateur pour confirmer son inscription

                        $email = (new TemplatedEmail())
                            ->from('infobookfolio@gmail.com')
                            ->to($user->getEmail())
                            ->subject('Confirmez votre inscription sur Bookfolio')
                            ->htmlTemplate('emails/confirmation_email.html.twig')
                            ->context(['user' => $user, 'token' => $accessToken]);
                        $mailer->send($email);

                        // 8) Message flash de confirmation indiquant que le mail a bien été envoyé

                        $this->get('session')->getFlashBag()
                            ->add('notice', [
                                'type' => 'success',
                                'title' => 'Vous y êtes presque !',
                                'message' => 'Un email de confirmation vient d\'être envoyé à l\'adresse ' . $user->getEmail() . ' afin de valider la création de votre book.',
                            ]);
                    } catch (\App\Exception\RegistrationException $exception) {
                        $this->addFlash('registration_error', $exception->getMessage());
                    }

                    return $this->redirectToRoute('home');
                }
            }

            return $this->render('registration/register.html.twig', ['registrationForm' => $form->createView()]);
        }
    }

    /**
     * @Route("/signup/confirm/{token}", name="confirm_account")
     *
     * @param $token
     * @param $user
     */
    public function confirmAccount($token, UserRepository $user, Request $request, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator, string $uploadDir, DesignRepository $designRepository, NotifierInterface $notifier, UploaderHelper $uploaderHelper): Response
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $user->findOneBy(['accessToken' => $token]);
        if (!$user) {
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // if (empty($user->getThumbnailField())) {
        //     $form = $this->createForm(RegistrationStepTwoFormType::class);
        // } else {
        //     $form = $this->createForm(RegistrationStepTwoWithoutAvatarFormType::class, ['validation_groups' => ['registration_step_two']]);
        // };

        $form = $this->createForm(RegistrationStepTwoFormType::class, ['validation_groups' => ['registration_step_two']]);

        $form->handleRequest($request);

        // $form->get('avatar')->setData($user->getThumbnail());
        //dd($form->getData());

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($_POST);

            $files = $request->files->get('files');
            $file = $form['avatar']->getData();
            $newFilename = $uploaderHelper->uploadAvatar($file, $user->getId(), null, true);
            $user->setThumbnail($newFilename);

            $user->setIsVerified(true);
            $user->setIsActive(true);
            $user->setAccessToken(0);
            $user->setUsername($form['username']->getData());
            $user->setLastname($form['lastname']->getData());
            $user->setFirstname($form['firstname']->getData());
            $user->setIsDemo(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            /**
             * On ajoute l'ID du user dans l'entité Address.
             */
            if (!$user->getAddress()) {
                $address = new Address();
                $address->setUser($user);
                $address->setFullAddress($form['fullAdresse']->getData());
                $address->setRoute($form['route']->getData());
                $address->setLocality($form['locality']->getData());
                $address->setAdminstrativeArea($form['adminstrativeArea']->getData());
                $address->setCountry($form['country']->getData());
                $address->setPostalCode($form['postalCode']->getData());
                $entityManager->persist($address);
                $entityManager->flush();
            }


            /**
             * On ajoute l'ID du user dans l'entité Physical.
             */
            if (!$user->getPhysical()) {
                $physical = new Physical();
                $physical->setUser($user);
                $entityManager->persist($physical);
                $entityManager->flush();
            }

            /**
             * On ajoute l'ID du user dans l'entité Options.
             */
            if (!$user->getOption()) {
                $option = new Option();
                $option->setUser($user);
                $entityManager->persist($option);
                $entityManager->flush();
            }

            /**
             * On ajoute l'ID du user dans l'entité Options.
             */
            if (!$user->getSocial()) {
                $social = new Social();
                $social->setUser($user);
                $entityManager->persist($social);
                $entityManager->flush();
            }

            /**
             * On ajoute l'ID du user dans l'entité Book.
             */
            if (!$user->getBook()) {
                $design = $designRepository->findOneBy(['isDefault' => true]);
                $book = new Book();
                $book->setUser($user);
                $book->setName($form['username']->getData());
                $book->setDesign($design);
                $book->setAllowSeo(true);
                $book->setShowContact(true);
                $book->setAllowComments(true);
                $book->setShowVisitorCounter(true);
                $entityManager->persist($book);
                $entityManager->flush();
            }

            /*
             * Ça y est ! L'inscription est totalement terminée.
             */
            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => 'Bienvenue parmi nous ' . $user->getFirstname() . ' :)',
                    'message' => $this->translator->trans('Votre book a bien été créé. Vous êtes à présent connecté !'),
                ]);

            $notification = (new Notification('Bookfolio - Inscription +1', ['email']))
                ->content($user->getLastname() . ' vient de rejoindre Bookfolio :)')
                ->importance(Notification::IMPORTANCE_HIGH);
            $notifier->send($notification, new Recipient('info@book-folio.fr'));

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
            // return $this->redirectToRoute('dashboard_profile_details');
        }

        return $this->render('registration/register_complete.html.twig', [
            'registrationForm' => $form->createView(),
            'token' => $token,
        ]);
    }

    /**
     * Avatar.
     *
     * @Route("upload/avatar/{token}", name="upload_avatar_register")
     */
    public function AjaxUploadAvatar($token, string $uploadDir, Request $request, UploaderHelper $uploaderHelper, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['accessToken' => $token]);
        if (!$user) {
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // $form = $this->createForm(RegistrationStepTwoFormType::class);
        // $form->handleRequest($request);
        // dd($request->files->get('registration_step_two_form'));

        $post = $request->files->get('registration_step_two_form');

        /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $files = $post['thumbnail'];

        foreach ($files as $file) {
            $newFilename = $uploaderHelper->uploadAvatar($file, $user->getId(), null, true);
            $configuration = [
                'limit' => 1,
                'fileMaxSize' => 5,
                'extensions' => ['image/*'],
                'title' => 'auto',
                'uploadDir' => $newFilename,
                'replace' => true,
                'editor' => [
                    'maxWidth' => 500,
                    'maxHeight' => 500,
                    'crop' => true,
                    'quality' => 100,
                ],
            ];

            if (isset($_POST['fileuploader']) && isset($_POST['name'])) {
                $name = str_replace(['/', '\\'], '', $_POST['name']);
                $editing = isset($_POST['editing']) && true == $_POST['editing'];

                if (is_file($configuration['uploadDir'] . $name)) {
                    $configuration['title'] = $name;
                    $configuration['replace'] = true;
                }
            }
            //On sauvegarde dans la bdd

            $user->setThumbnail($newFilename);
            $user->setUpdatedAt(new DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // change file's public data
            if (!empty($file)) {
                $item = $files[0];

                $file = [
                    'title' => $item->getClientOriginalName(),
                    'name' => $item->getClientOriginalName(),
                    'size' => $item->getSize(),
                    'size2' => $item->getSize(),
                ];
            }
        }

        $data = [
            'files' => $files,
            'isSuccess' => true,
            'hasWarnings' => false,
            'warnings' => [],
        ];
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('home');
        } else {
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $authenticationUtils->getLastAuthenticationError()
            ]);
        }
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Génération du Token.
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    public function listExperienceOfMetier(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $listExperienceRepository = $em->getRepository('App:Experience');

        $experiences = $listExperienceRepository->createQueryBuilder('q')
            ->where('q.profession = :professionid')
            ->setParameter('professionid', $request->query->get('professionid'))
            ->getQuery()
            ->getResult();

        $responseArray = [];
        foreach ($experiences as $experience) {
            $responseArray[] = [
                'id' => $experience->getId(),
                'title' => $experience->getTitle(),
            ];
        }

        return new JsonResponse($responseArray);
    }

    public function resize($filename)
    {
        list($iwidth, $iheight) = getimagesize($filename);
        $ratio = $iwidth / $iheight;
        $width = self::WIDTH;
        $height = self::HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }
        $photo = $this->imagine->open($filename);
        $photo->resize(new Box($width, $height))->save($filename);

        return $photo;
    }
}
