<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Entity\Inbox;
use App\Entity\InboxReply;
use App\Entity\Notification;
use App\Repository\EventsRepository;
use App\Repository\FollowRepository;
use App\Repository\InboxReplyRepository;
use App\Repository\InboxRepository;
use App\Repository\SortListFollowRepository;
use App\Repository\UserRepository;
use App\Service\AvalableCreditService;
use App\Service\AwsImageService;
use App\Service\Mailer;
use App\Service\RandomFlashMessage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Liip\ImagineBundle\Service\FilterService;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Urodoz\Truncate\TruncateService;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @IsGranted("ROLE_USER")
 * @Route("dashboard/relations", name="relation_")
 */
class FollowController extends AbstractController
{
    public function __construct(
        TranslatorInterface $translator,
        AvalableCreditService $avalableCreditService,
        RandomFlashMessage $randomFlashMessage,
        InboxReplyRepository $inboxReplyRepository,
        Breadcrumbs $breadcrumbs,
        Mailer $mailer,
        SortListFollowRepository $sortListFollowRepository,
        FilterService $filterService,
        AwsImageService $awsImageService
    ) {
        $this->translator = $translator;
        $this->avalableCreditService = $avalableCreditService;
        $this->randomFlashMessage = $randomFlashMessage;
        $this->inboxReplyRepository = $inboxReplyRepository;
        $this->breadcrumbs = $breadcrumbs;
        $this->mailer = $mailer;
        $this->sortListFollowRepository = $sortListFollowRepository;
        $this->filterService = $filterService;
        $this->awsImageService = $awsImageService;
    }

    /**
     * Gestion des abonnés.
     *
     * @Route("/followers", name="followers")
     */
    public function Followers(Request $request, FollowRepository $repos, PaginatorInterface $paginator)
    {
        //if ($this->getUser()->hasActiveSubscription()) {
        $this->breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $this->breadcrumbs->addItem('Abonnés');

        $query = $repos->findBy(['friend' => $this->getUser()], ['createdAt' => 'DESC']);
        $followers = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->getParameter('PerPage')
        );

        return $this->render('dashboard/relations/followers.html.twig', [
            'followers' => $followers,
            'title' => $this->translator->trans('Vos abonnés'),
        ]);
        /*} else {
            return $this->redirectToRoute('account_subscription');
        }*/
    }

    /**
     * Gestion des abonnements.
     *
     * @Route("/following", name="following")
     */
    public function Following(FollowRepository $followRepository, PaginatorInterface $paginator, Request $request)
    {
        $this->breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $this->breadcrumbs->addItem('Abonnements');

        $query = $followRepository->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        $followings = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->getParameter('PerPage')
        );

        return $this->render('dashboard/relations/following.html.twig', [
            'followings' => $followings,
            'title' => $this->translator->trans('Vos abonnements'),
        ]);
    }

    /**
     * Récupération des followers en JSON.
     *
     * @Route("/followers/json/{page}", name="json_followers")
     */
    public function jsonFollowers(Request $request, FollowRepository $followRepository, $page, FilterService $filterService, PaginatorInterface $paginator, FollowRepository $followRepos)
    {
        $sortBy = $request->query->get('sortBy');
        $sort = $this->sortListFollowRepository->findOneBy(['title' => $sortBy]);

        $truncateService = new TruncateService();
        $query = $followRepository->findAllFollowers($this->getUser(), $sort->getTitle());
        $pageSize = '20';
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $users = [];
        foreach ($paginator as $follower) {
            if (count($follower->getUser()->getGalleries()) <= 1) {
                $countGalleries = '<span class="fcounter">' . count($follower->getUser()->getGalleries()) . '</span> galerie';
            } else {
                $countGalleries = '<span class="fcounter">' . count($follower->getUser()->getGalleries()) . '</span> galeries';
            }

            if (count($follower->getUser()->getImages()) <= 1) {
                $countImages = '<span class="fcounter">' . count($follower->getUser()->getImages()) . '</span> publication';
            } else {
                $countImages = '<span class="fcounter">' . count($follower->getUser()->getImages()) . '</span> publications';
            }

            $users[] = [
                'identity' => [
                    'uuid' => $follower->getUser()->getUuid(),
                    'url' => $this->get('router')->generate('portfolio_index', ['name' => $follower->getUser()->getBook()->getName()]),
                    'avatar' => $this->awsImageService->getPathAvatar($follower->getUser()->getAvatar()),
                    'experience' => $follower->getUser()->getProfession()->getTitle(),
                    'location' => $follower->getUser()->getAddress()->getFullAddress(),
                    'locationTruncate' => $truncateService->truncate($follower->getUser()->getAddress()->getFullAddress(), 30),
                    'fullname' => $follower->getUser()->getFullname(),
                    'username' => $follower->getUser()->getUsername(),
                    'countFolders' => count($follower->getUser()->getGalleries()),
                    'countImages' => count($follower->getUser()->getImages()),
                    'countVideos' => count($follower->getUser()->getVideos()),
                    'notify' => ($follower->getNotificationsInterface() ? true : false),
                    'followed' => ($followRepos->findOneBy(['user' => $this->getUser(), 'friend' => $follower->getUser()]) ? true : false),
                    'certified' => $follower->getUser()->getCertified(),
                ],
            ];
        }

        $datas = [
            'users' => $users,
            'totalPages' => $pagesCount,
            'sortList' => $this->getListSort(),
        ];

        echo json_encode($datas);
        exit;
    }

    /**
     * Récupération des following en JSON.
     *
     * @Route("/following/json/{page}", name="json_following")
     */
    public function jsonFollowing(Request $request, FollowRepository $followRepository, $page, FilterService $filterService, PaginatorInterface $paginator, FollowRepository $followRepos, SortListFollowRepository $sortListFollowRepository)
    {
        $sortBy = $request->query->get('sortBy');
        $sort = $sortListFollowRepository->findOneBy(['title' => $sortBy]);

        $truncateService = new TruncateService();
        $query = $followRepository->findAllFollowing($this->getUser(), $sort->getTitle());
        $pageSize = '20';
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $users = [];
        foreach ($paginator as $follow) {

            $users[] = [
                'identity' => [
                    'uuid' => $follow->getFriend()->getUuid(),
                    'url' => $this->get('router')->generate('portfolio_index', ['name' => $follow->getFriend()->getBook()->getName()]),
                    'avatar' => $this->awsImageService->getPathAvatar($follow->getFriend()->getAvatar()),
                    'experience' => $follow->getFriend()->getProfession()->getTitle(),
                    'location' => $follow->getFriend()->getAddress()->getFullAddress(),
                    'locationTruncate' => $truncateService->truncate($follow->getFriend()->getAddress()->getFullAddress(), 30),
                    'fullname' => $follow->getFriend()->getFullname(),
                    'username' => $follow->getFriend()->getUsername(),
                    'countFolders' => count($follow->getFriend()->getGalleries()),
                    'countImages' => count($follow->getFriend()->getImages()),
                    'countVideos' => count($follow->getFriend()->getVideos()),
                    'notify' => ($follow->getNotificationsInterface() ? true : false),
                    'followed' => ($followRepos->findOneBy(['user' => $this->getUser(), 'friend' => $follow->getFriend()]) ? true : false),
                    'certified' => $follow->getFriend()->getCertified(),
                ],
            ];
        }

        $datas = [
            'users' => $users,
            'totalPages' => $pagesCount,
            'sortList' => $this->getListSort(),
        ];

        $response = new JsonResponse($datas);
        $response->setSharedMaxAge(3600);
        return $response;
        exit;
    }

    public function getListSort()
    {
        $sortList = [];
        $sortListFollow = $this->sortListFollowRepository->findAllSort();
        foreach ($sortListFollow as $value) {
            $sortList[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
                'content' => $value->getDescription(),
            ];
        }

        return $sortList;
    }

    /**
     * Ajout d'une notification.
     *
     * @Route("/addnotify/user/{username}", name="relation_add_notify")
     */
    public function AddNotify(FollowRepository $followRepository, $username, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);
        if ($user) {
            $follow = $followRepository->findOneBy(['user' => $this->getUser(), 'friend' => $user, 'notificationsInterface' => false]);
            if ($follow) {
                $follow->setNotificationsInterface(true);
                $em->persist($follow);
                $em->flush();

                return new Response(true);
            }
        } else {
            return new Response(false);
        }
        exit;
    }

    /**
     * Suppression d'une notification.
     *
     * @Route("/removenotify/user/{username}", name="remove_notify")
     */
    public function RemoveNotify(FollowRepository $followRepository, $username, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);
        if ($user) {
            $follow = $followRepository->findOneBy(['friend' => $this->getUser(), 'user' => $user->getId()]);
            if ($follow) {
                $follow->setNotificationsInterface(false);
                $em->persist($follow);
                $em->flush();

                return new Response(true);
            }
        } else {
            return new Response(false);
        }
        exit;
    }

    /**
     * Envoi du message.
     *
     * @Route("/send/message/user/{uuid}/message/{message}", name="send_message_contact", methods={"GET"})
     */
    public function SendMessage($uuid, $message, UserRepository $userRepository, InboxRepository $inboxRepository, EntityManagerInterface $em, MailerInterface $mailer)
    {
        // On vérifie que l'utilisateur existe
        $book = $userRepository->findOneBy(['uuid' => $uuid]);
        if ($book) {
            $inboxReply = new InboxReply();

            // On vérifie qu'une conversation existe déjà
            $inboxExist = $inboxRepository->findOneBy(['book' => $book, 'sender' => $this->getUser()]);
            if (!$inboxExist) {
                $uuid = Uuid::uuid4();
                $inbox = new Inbox();
                $inbox->setBook($book->getUser());
                $inbox->setSender($this->getUser());
                $inbox->setStatus('unread');
                $inbox->setCreatedAt(new \DateTime('now'));
                $inbox->setUuid($uuid);
                $inbox->setIsFavorites(false);
                $inbox->setIsReport(false);
                $em->persist($inbox);

                $inboxReply->setInbox($inbox);
            } else {
                $inboxReply->setInbox($inboxExist);
            }
            // Conversation
            $inboxReply->setUser($this->getUser());
            $inboxReply->setText($message);
            $inboxReply->setCreatedAt(new \DateTime('now'));
            $em->persist($inboxReply);

            $em->flush();

            // Ensuite on envoi un mail au destinataire
            $this->mailer->sendEmailInbox($book, $message, $this->getUser());

            return new Response(true);
        } else {
            return new Response(false);
        }
    }

    /**
     * AddFollow.
     *
     * @Route("/follow/user/{username}", name="add_follow")
     */
    public function AddFollow(EventsRepository $eventsRepository, $username, UserRepository $userRepository, EntityManagerInterface $em, FollowRepository $followRepository): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);
        $follow = $followRepository->findOneBy(['user' => $this->getUser(), 'friend' => $user]);
        if ($user && !$follow) {
            $follow = new Follow();
            $follow->setUser($this->getUser());
            $follow->setFriend($user);
            $follow->setNotificationsInterface(false);
            $follow->setCreatedAt(new \DateTime('now'));
            $em->persist($follow);
            $em->flush();

            // Envoi de la notification à l'utilisiteur concerné
            $event = $eventsRepository->findOneBy(['type' => 'follow']);
            $notification = new Notification();
            $notification->setUserWhoFiredEvent($this->getUser());
            $notification->setUserToNotify($user);
            $notification->setEvent($event);
            $notification->setSeenByUser('no');
            $notification->setCreatedAt(new \DateTime('now'));
            $em->persist($notification);
            $em->flush();

            // Envoie du mail si option activé par le destinataire
            if (true == $user->getOption()->getFollow()) {
                if ($user != $this->getUser()) {
                    $this->mailer->sendEmailNewFollower($user, $this->getUser());
                }
            }

            return new Response(true);
        } else {
            return new Response(false, 403);
        }
        exit;
    }

    /**
     * Suppresion follow.
     *
     * @Route("follow/unfollow/user/{username}", name="remove_follow")
     */
    public function RemoveFollow($username, UserRepository $repos, EntityManagerInterface $em, FollowRepository $reposFollow): Response
    {
        $repos = $repos->findOneBy(['username' => $username]);

        if ($repos) {
            $follow = $reposFollow->findOneBy(['user' => $this->getUser(), 'friend' => $repos->getId()]);
            $em->remove($follow);
            $em->flush();
            // Envoi de la notification

            return new Response(true);
        } else {
            return new Response(false, 403);
        }
        exit;
    }

    /**
     * Récupération de la liste des book à suivre.
     *
     * @Route("/follow/suggest/all", name="short_suggest_book_to_follow")
     */
    public function ShortSuggestBooksToFollow(UserRepository $userRepository, FollowRepository $followRepos)
    {
        $query = $userRepository->findFollowUsers(10, $this->getUser());
        $users = [];

        $truncateService = new TruncateService();
        foreach ($query as $user) {

            $followers = [];
            $followersData = $followRepos->findBy(['friend' => $user], ['createdAt' => 'DESC']);
            foreach ($followersData as $follower) {
                $followers[] = [
                    'avatar' => $this->awsImageService->getPathAvatar($follower->getUser()->getAvatar()),
                    'fullname' => $follower->getUser()->getFullname(),
                ];
            }


            $users[] = [
                'identity' => [
                    'uuid' => $user->getUuid(),
                    'url' => $this->get('router')->generate('portfolio_index', ['name' => $user->getBook()->getName()]),
                    'avatar' => $this->awsImageService->getPathAvatar($user->getAvatar()),
                    'profession' => $user->getProfession()->getTitle(),
                    'location' => $user->getAddress()->getFullAddress(),
                    'locationTruncate' => $truncateService->truncate($user->getAddress()->getFullAddress(), 25),
                    'fullname' => $truncateService->truncate($user->getFullname(), 25),
                    'username' => $user->getUsername(),
                    'countFolders' => count($user->getGalleries()),
                    'countImages' => count($user->getImages()),
                    'countVideos' => count($user->getVideos()),
                    //'followed' => ($followRepos->findOneBy(['user' => $this->getUser(), 'friend' => $user]) ? true : false),
                    'certified' => $user->getCertified(),
                ],
                'followers' => $followers,
            ];
        }

        return new JsonResponse($users);
        exit;
    }
}
