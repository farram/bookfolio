<?php

namespace App\Controller;

use DateTime;
use App\Entity\Book;
use App\Entity\User;
use Imagine\Image\Box;
use App\Entity\Gallery;
use App\Service\Mailer;
use Imagine\Gd\Imagine;
use App\Form\BookFormType;
use App\Form\DomainFormType;
use App\Service\PlanService;
use App\Service\FileUploader;
use App\Service\UploaderHelper;
use App\Service\AwsImageService;
use App\Repository\UserRepository;
use App\Service\RandomFlashMessage;
use App\Repository\FollowRepository;
use App\Repository\ImagesRepository;
use App\Repository\OptionRepository;
use Urodoz\Truncate\TruncateService;
use App\Repository\AnnoncesRepository;
use App\Service\AvalableCreditService;
use App\Repository\ImageLikeRepository;
use App\Repository\ExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ImageCommentRepository;
use App\Repository\NotificationRepository;
use App\Repository\ReleaseNotesRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Bundle\TimeBundle\DateTimeFormatter;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * @Route("/dashboard", name="dashboard_")
 */
class DashboardController extends AbstractController
{
    /**
     * @param Request      $request
     * @param string       $uploadDir
     * @param FileUploader $uploader
     *
     * @return Response
     */
    private $imagine;

    private $passwordEncoder;

    public function __construct(
        Security $security,
        FilterService $filterService,
        PasswordHasherFactoryInterface $passwordHasherFactoryInterface,
        TranslatorInterface $translator,
        UserRepository $userRepository,
        AvalableCreditService $avalableCreditService,
        RandomFlashMessage $randomFlashMessage,
        Mailer $mailer,
        Breadcrumbs $breadcrumbs,
        AwsImageService $awsImageService,
        PlanService $planService
    ) {
        $gallery = new Gallery();
        $this->imagine = new Imagine();
        $this->security = $security;
        $this->passwordEncoder = $passwordHasherFactoryInterface->getPasswordHasher($gallery);
        $this->translator = $translator;
        $this->userRepository = $userRepository;
        $this->avalableCreditService = $avalableCreditService;
        $this->randomFlashMessage = $randomFlashMessage;
        $this->mailer = $mailer;
        $this->filterService = $filterService;
        $this->breadcrumbs = $breadcrumbs;
        $this->awsImageService = $awsImageService;
        $this->planService = $planService;
    }

    /**
     * Tableau de bord.
     *
     * @Route("/", name="index")
     */
    public function index(UserRepository $userRepository, AnnoncesRepository $annoncesRepository, ReleaseNotesRepository $releaseNotesRepository)
    {
        $new_books = $userRepository->findNewUsers(4, $this->security->getUser());
        $annonces = $annoncesRepository->findBy(['isActive' => 1], ['createdAt' => 'DESC'], 5);
        $releases = $releaseNotesRepository->findActiveBy();
        return $this->render('dashboard/index.html.twig', [
            'new_books' => $new_books,
            'annonces' => $annonces,
            'releases' => $releases,
            'title' => $this->translator->trans('Tableau de bord'),
        ]);
    }


    /**
     * Fil d'actualité.
     *
     * @Route("/feed/{page}", name="feed_medias")
     */
    public function FeedMedias(
        Request $request,
        FollowRepository $followRepos,
        $page,
        ImageCommentRepository $imageCommentRepository,
        ImageLikeRepository $imageLikeRepository,
        ImagesRepository $imagesRepository,
        DateTimeFormatter $dateTimeFormatter
    ) {
        $displayBy = $request->query->get('displayBy');
        $query = $imagesRepository->findNewFresh($this->security->getUser(), $displayBy);
        $pageSize = 4;
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $items = [];

        $filters = $this->get('twig')->getFunctions();
        $callable = $filters['uploaded_asset']->getCallable();

        $now = new \DateTime();
        $truncateService = new TruncateService();

        foreach ($paginator as $image) {
            // Récupération des likes
            $getLikes = $imageLikeRepository->findBy(['image' => $image], ['id' => 'DESC']);
            $likes = [];
            if ($getLikes) {
                foreach ($getLikes as $like) {
                    $likes[] = [
                        'identity' => [
                            'url' => $this->get('router')->generate('portfolio_index', ['name' => $like->getUser()->getBook()->getName()]),
                            'avatar' => $this->awsImageService->getPathAvatar($like->getUser()->getAvatar()),
                            'fullname' => $like->getUser()->getFullname(),
                            'username' => $like->getUser()->getUsername(),
                            'followed' => ($followRepos->findOneBy(['user' => $this->security->getUser(), 'friend' => $like->getUser()]) ? true : false),
                            'profession' => $like->getUser()->getProfession()->getTitle(),
                        ],
                    ];
                }
            }

            // Récupération des commentaires
            $getAllComments = $imageCommentRepository->findBy(['image' => $image, 'isActive' => true, 'parent' => null], ['createdAt' => 'DESC']);
            $countComments = $imageCommentRepository->findBy(['image' => $image, 'isActive' => true]);
            $comments = [];

            if ($getAllComments) {
                foreach ($getAllComments as $comment) {
                    $commentsChild = [];
                    $getChilComments = $imageCommentRepository->findBy(['image' => $image, 'parent' => $comment, 'isActive' => true], ['createdAt' => 'ASC']);
                    foreach ($getChilComments as $child) {
                        $commentsChild[] = [
                            'id' => $child->getId(),
                            'content' => $child->getContent(),
                            'date' => $dateTimeFormatter->formatDiff($child->getCreatedAt(), $now),
                            'user' => [
                                'avatar' => $this->awsImageService->getPathAvatar($child->getUser()->getAvatar()),
                                'fullname' => $child->getUser()->getFullname(),
                                'uuid' => $child->getUser()->getUuid(),
                            ],
                        ];
                    }

                    $comments[] = [
                        'id' => $comment->getId(),
                        'content' => $comment->getContent(),
                        'date' => $dateTimeFormatter->formatDiff($comment->getCreatedAt(), $now),
                        'child' => $commentsChild,
                        'user' => [
                            'avatar' => $this->awsImageService->getPathAvatar($comment->getUser()->getAvatar()),
                            'fullname' => $comment->getUser()->getFullname(),
                            'uuid' => $comment->getUser()->getUuid(),
                        ],
                    ];
                }
            }

            $agoTime = $dateTimeFormatter->formatDiff($image->getCreatedAt(), $now);
            $thumb = $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumb_medium');
            $items[] = [
                'id' => $image->getId(),
                'path' => $callable($image->getImagePath()),
                'thumb' => $thumb,
                'title' => $image->getTitle(),
                'copyright' => $image->getCopyright(),
                'date' => $agoTime,
                'likes' => $likes,
                'comments' => $comments,
                'allowLikes' => ((1 == $image->getAllowLikes()) ? true : false),
                'allowComments' => ((1 == $image->getAllowComments()) ? true : false),
                'alreadyLike' => $this->alreadyLike($imageLikeRepository, $imagesRepository, $image->getId()),
                'countComments' => $countComments,
                'user' => [
                    'url' => $this->get('router')->generate('portfolio_index', ['name' => $image->getUser()->getBook()->getName()]),
                    'avatar' => $this->awsImageService->getPathAvatar($image->getUser()->getAvatar()),
                    'fullname' => $truncateService->truncate($image->getUser()->getFullname(), 20),
                    'shortname' => $image->getUser()->getFirstname() . ' ' . substr(ucfirst($image->getUser()->getLastname()), 0, 1) . '.',
                    'username' => $image->getUser()->getUsername(),
                    'profession' => $image->getUser()->getProfession()->getTitle(),
                    'location' => $image->getUser()->getAddress()->getFullAddress(),
                    'followed' => ($followRepos->findOneBy(['user' => $this->getUser(), 'friend' => $image->getUser()]) ? true : false),
                    'uuid' => $this->getUser()->getUuid(),
                ],
            ];
        }

        $datas = [
            'items' => $items,
            'totalPages' => $pagesCount,
            'user' => [
                'uuid' => $this->getUser()->getUuid(),
            ],
        ];

        $response = new JsonResponse($datas);
        $response->setSharedMaxAge(3600);
        return $response;
        exit;
    }

    public function alreadyLike($imageLikeRepository, $imagesRepository, $imageId)
    {
        $image = $imagesRepository->find($imageId);
        if ($image) {
            $alreadylike = $imageLikeRepository->findOneBy(['user' => $this->getUser(), 'image' => $image]);
            if ($alreadylike) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Récupération des nouveaux books.
     *
     * @Route("/users/new", name="new_books")
     */
    public function NewBooks(UserRepository $userRepository, FollowRepository $followRepos, ImagesRepository $imagesRepository)
    {
        $query = $userRepository->findNewUsers(4, $this->security->getUser());

        $users = [];
        $truncateService = new TruncateService();
        foreach ($query as $user) {
            $users[] = [
                'url' => $this->get('router')->generate('portfolio_index', ['name' => $user->getBook()->getName()]),
                'avatar' => $this->awsImageService->getPathAvatar($user->getAvatar()),
                'profession' => $truncateService->truncate($user->getProfession()->getTitle(), 25),
                'locationTruncate' => $truncateService->truncate($user->getAddress()->getFullAddress(), 30),
                'fullname' => $truncateService->truncate($user->getFullname(), 30),
                'certified' => $user->getCertified(),
            ];
        }

        $datas = [
            'total' => count($users),
            'data' => $users,
        ];

        return new JsonResponse($datas);
        exit;
    }

    /**
     * Configuration du book.
     *
     * @Route("/manage/book/settings", name="manage_book_settings")
     */
    public function ManageBookDetails(Breadcrumbs $breadcrumbs, Request $request)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans('Configuration'));

        $user = $this->security->getUser();

        $form = $this->createForm(BookFormType::class, $user->getBook());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //On met à jour la date de mise à jour
            $user->setUpdatedAt(new DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $physical = $entityManager->getRepository(Book::class)->findOneBy(['user' => $user->getId()]);
            $entityManager->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Vos informations ont bien été sauvegardées'),
                ]);

            return $this->redirect($request->headers->get('referer'));
        }

        $form_domain = $this->createForm(DomainFormType::class);

        return $this->render('dashboard/manage/book/details.html.twig', [
            'form' => $form->createView(),
            'form_domain' => $form_domain->createView(),
            'title' => $this->translator->trans('Configuration de votre book'),
        ]);
    }

    /**
     * Gestion des notifications.
     *
     * @Route("/notifications", name="notifications")
     */
    public function Notifications(NotificationRepository $notificationRepository, Breadcrumbs $breadcrumbs, EntityManagerInterface $em)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Notifications');

        $notifications = $notificationRepository->findBy(['userToNotify' => $this->security->getUser()]);
        $getSeenNotifications = $notificationRepository->findBy(['userToNotify' => $this->security->getUser(), 'seenByUser' => 'no']);
        if ($getSeenNotifications) {
            foreach ($getSeenNotifications as $getSeenNotification) {
                $getSeenNotification->setSeenByUser('yes');
                $em->persist($getSeenNotification);
                $em->flush();
            }
        }

        return $this->render('dashboard/notifications/all.html.twig', [
            'notifications' => $notifications,
            'title' => $this->translator->trans('Vos notifications'),
        ]);
    }

    /**
     * Récupération des notifications.
     *
     * @Route("/notifications/{page}/dispComments/{showComments}/dispLikes/{showLikes}/dispFollows/{showFollows}", name="api_notifications")
     */
    public function ApiNotifications(FollowRepository $followRepository, $page, $showComments, $showLikes, $showFollows, NotificationRepository $notificationRepository, DateTimeFormatter $dateTimeFormatter)
    {
        $query = $notificationRepository->findAllOfMe($this->security->getUser(), $showComments, $showLikes, $showFollows);
        $pageSize = '10';
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $notifications = [];
        $now = new \DateTime();

        $filters = $this->get('twig')->getFunctions();
        $callable = $filters['uploaded_asset']->getCallable();

        foreach ($paginator as $notification) {
            switch ($notification->getEvent()->getType()) {
                case 'follow':
                    $media = null;
                    $comment = null;
                    break;
                case 'comment':
                    $thumb = $this->filterService->getUrlOfFilteredImage($notification->getMedia()->getImagePath(), 'thumb_16_9_medium');
                    $media = [
                        'path' => $callable($notification->getMedia()->getImagePath()),
                        'id' => $notification->getMedia()->getId(),
                        'thumb' => $thumb,
                        'title' => $notification->getMedia()->getTitle(),
                        'url' => $this->get('router')->generate('media_show', ['gallerySlug' => $notification->getMedia()->getGallery()->getSlug(), 'mediaId' => $notification->getMedia()->getId()]),
                    ];
                    $comment = [
                        'text' => (!is_null($notification->getComment()) ? $notification->getComment()->getContent() : null),
                        'url' => $this->get('router')->generate('media_show', ['gallerySlug' => $notification->getMedia()->getGallery()->getSlug(), 'mediaId' => $notification->getMedia()->getId()]),
                    ];
                    break;
                case 'like':
                    $thumb = $this->filterService->getUrlOfFilteredImage($notification->getMedia()->getImagePath(), 'thumb_16_9_medium');
                    $media = [
                        'path' => $callable($notification->getMedia()->getImagePath()),
                        'id' => $notification->getMedia()->getId(),
                        'thumb' => $thumb,
                        'title' => $notification->getMedia()->getTitle(),
                        'url' => $this->get('router')->generate('media_show', ['gallerySlug' => $notification->getMedia()->getGallery()->getSlug(), 'mediaId' => $notification->getMedia()->getId()]),
                    ];
                    $comment = null;
                    break;
                default:
                    break;
            }

            $notifications[] = [
                'id' => $notification->getId(),
                'text' => $notification->getEvent()->getText(),
                'icon' => $notification->getEvent()->getIcon(),
                'color' => $notification->getEvent()->getColor(),
                'type' => $notification->getEvent()->getType(),
                'date' => $dateTimeFormatter->formatDiff($notification->getCreatedAt(), $now),
                'user' => [
                    'identity' => [
                        'url' => $this->get('router')->generate('portfolio_index', ['name' => $notification->getUserWhoFiredEvent()->getBook()->getName()]),
                        'avatar' => $this->awsImageService->getPathAvatar($notification->getUserWhoFiredEvent()->getAvatar()),
                        'fullname' => $notification->getUserWhoFiredEvent()->getFullname(),
                        'username' => $notification->getUserWhoFiredEvent()->getUsername(),
                        'followed' => ($followRepository->findOneBy(['user' => $this->security->getUser(), 'friend' => $notification->getUserWhoFiredEvent()]) ? true : false),
                    ],
                ],
                'media' => $media,
                'comment' => $comment,
            ];
        }
        $datas = [
            'notifications' => $notifications,
            'pagesCount' => $pagesCount,
        ];

        echo json_encode($datas);
        exit;
    }

    /**
     * Gestion des notifications.
     *
     * @Route("/user/{uuid}/notifications/{action}", name="user_notifications_off")
     */
    public function UserNotifications(Request $request, User $user, $action, FollowRepository $followRepos, EntityManagerInterface $em)
    {
        // 1.   On vérifie que l'utilisateur existe bien.
        if ($user) {
            // 2.   On vérifie que l'utilisateur connecté follow bien la personne
            $follow = $followRepos->findOneBy(['user' => $this->security->getUser(), 'friend' => $user->getId()]);

            // 3.   Si c'est le cas, on gère la demande
            if ($follow) {
                switch ($action) {
                    case 'off':
                        $status = false;
                        break;
                    case 'on':
                        $status = 'true';
                        break;
                }

                $follow->setNotificationsInterface($status);
                $em->persist($follow);
                $em->flush();
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/experience/type/{type}", name="json_experience")
     */
    public function jsonExperience($type, ExperienceRepository $experienceRepos)
    {
        $list = $experienceRepos->findBy(['profession' => $type]);
        $datas = [];
        foreach ($list as $value) {
            $datas[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }
        echo json_encode($datas);
        exit;
    }

    /**
     * Avatar.
     *
     * @Route("/manage/book/upload/avatar", name="ajax_upload_avatar")
     */
    public function AjaxUploadAvatar(string $uploadDir, Request $request, UploaderHelper $uploaderHelper)
    {
        /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $files = $request->files->get('files');

        foreach ($files as $file) {
            $newFilename = $uploaderHelper->uploadAvatar($file, $this->getUser()->getId(), null, true);
            //dd($newFilename);

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
            $user = $this->getUser();

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
     * Avatar.
     *
     * @Route("/profile/remove/avatar", name="ajax_remove_avatar")
     */
    public function AjaxRemoveAvatar(string $uploadDir)
    {
        if (isset($_POST['file'])) {
            $file = $uploadDir . '/' . str_replace(['/', '\\'], '', $_POST['file']);
            if (file_exists($file));
        }
    }

    public function doResize($imageLocation, $imageDestination, array $options = null)
    {
        $newWidth = $newHeight = 0;
        list($width, $height) = getimagesize($imageLocation);

        if (isset($options['newWidth']) || isset($options['newHeight'])) {
            if (isset($options['newWidth']) && isset($options['newHeight'])) {
                $newWidth = $options['newWidth'];
                $newHeight = $options['newHeight'];
            } elseif (isset($options['newWidth'])) {
                $deviationPercentage = (($width - $options['newWidth']) / (0.01 * $width)) / 100;

                $newWidth = $options['newWidth'];
                $newHeight = $height - ($height * $deviationPercentage);
            } else {
                $deviationPercentage = (($height - $options['newHeight']) / (0.01 * $height)) / 100;

                $newWidth = $width - ($width * $deviationPercentage);
                $newHeight = $options['newHeight'];
            }
        } else {
            // reduce image size up to 20% by default
            $reduceRatio = isset($options['reduceRatio']) ? $options['reduceRatio'] : 20;

            $newWidth = $width * ((100 - $reduceRatio) / 100);
            $newHeight = $height * ((100 - $reduceRatio) / 100);
        }
        $photo = $this->imagine->open($imageLocation);
        $photo->thumbnail(new Box((int) $newWidth, (int) $newHeight))->save($imageDestination, ['quality' => isset($options['quality']) ? $options['quality'] : 100]);
    }
}
