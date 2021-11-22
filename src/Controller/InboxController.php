<?php

namespace App\Controller;

use App\Entity\InboxReply;
use App\Form\InboxReplyFormType;
use App\Repository\FollowRepository;
use App\Repository\ImagesRepository;
use App\Repository\InboxReplyRepository;
use App\Repository\InboxRepository;
use App\Service\AvalableCreditService;
use App\Service\RandomFlashMessage;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use YoHang88\LetterAvatar\LetterAvatar;

/**
 * @IsGranted("ROLE_USER")
 * @Route("dashboard/inbox", name="inbox_")
 */
class InboxController extends AbstractController
{
    public function __construct(FilterService $filterService, TranslatorInterface $translator, AvalableCreditService $avalableCreditService, RandomFlashMessage $randomFlashMessage, InboxReplyRepository $inboxReplyRepository)
    {
        $this->translator = $translator;
        $this->avalableCreditService = $avalableCreditService;
        $this->randomFlashMessage = $randomFlashMessage;
        $this->inboxReplyRepository = $inboxReplyRepository;
        $this->imagineServiceFilter = $filterService;
    }

    public function getFavoritesListInboxes(InboxRepository $inboxRepository)
    {
        $listInboxes = [];
        $inboxes = $inboxRepository->findBy(['book' => $this->getUser(), 'isFavorites' => true], ['createdAt' => 'DESC']);
        foreach ($inboxes as $inbox) {
            $listInboxes[] = [
                'id' => $inbox->getId(),
                'uuid' => $inbox->getUuid(),
                'fullname' => $inbox->user->getFullname(),
                'date' => $inbox->getCreatedAt(),
                //'message' => $inbox->getMessage(),
                'avatar' => new LetterAvatar($inbox->user->getFullname(), 'square', 64),
                'status' => $inbox->getStatus(),
                'favorites' => $inbox->getIsFavorites(),
            ];
        }

        return $listInboxes;
    }

    /**
     * Liste des messages reçus.
     *
     * @Route("/", name="all")
     */
    public function Inbox(Request $request, InboxRepository $inboxRepository, Breadcrumbs $breadcrumbs, EntityManagerInterface $em)
    {
        if ($this->getUser()->hasActiveSubscription()) {
            $title = $this->translator->trans('Messagerie');
            $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
            $breadcrumbs->addItem($this->translator->trans('Tous les messages'));

            $lastMessage = [];
            $inbox = $inboxRepository->findLast($this->getUser());

            $inboxReply = new InboxReply();
            $form = $this->createForm(InboxReplyFormType::class, $inboxReply);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $add = new InboxReply();
                $add->setInbox($inbox);
                $add->setUser($this->getUser());
                $add->setText($form->get('text')->getData());
                $add->setCreatedAt(new \DateTime());

                $em->persist($add);
                $em->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->randomFlashMessage->getTitle(),
                        'message' => $this->translator->trans('Votre message a bien été envoyé.'),
                    ]);

                return $this->redirect($request->headers->get('referer'));
            }

            if ($inbox) {
                $lastMessage = [
                    'id' => $inbox->getId(),
                    'uuid' => $inbox->getUuid(),
                    'date' => $inbox->getCreatedAt(),
                    'favorites' => $inbox->getIsFavorites(),
                    'user' => [
                        'id' => $inbox->getSender()->getId(),
                        'uuid' => $inbox->getSender()->getUuid(),
                        'url' => $inbox->getSender()->getLinkBook(),
                        'fullname' => $inbox->getSender()->getFullname(),
                        'avatar' => $this->imagineServiceFilter->getUrlOfFilteredImage($inbox->getSender()->getUser()->getAvatar(), 'avatar'),
                        'profession' => $inbox->getSender()->getUser()->getProfession()->getTitle(),
                        'location' => $inbox->getSender()->getUser()->getUser()->getAddress()->getFullAddress(),
                        'about' => $inbox->getSender()->getUser()->getAbout(),
                        'social' => $inbox->getSender()->getSocial(),
                        'images' => $inbox->getSender()->getImages(),
                        'book' => [
                            'name' => $inbox->getSender()->getBook()->getName(),
                        ],
                    ],
                    'replies' => $inbox->getInboxReplies(),
                ];
            }

            return $this->render('dashboard/inbox/all.html.twig', [
                'title' => $title,
                'listInboxes' => $this->getListInboxes($inboxRepository),
                'lastMessage' => $lastMessage,
                'form' => $form->createView(),
            ]);
        } else {

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'warning',
                    'title' => 'Accès restreint',
                    'message' => $this->translator->trans("Vous devez disposer d'une formule Awesome ou Pro pour utiliser cette fonctionnalité."),
                ]);

            return $this->redirectToRoute('pricing_all');
        }
    }

    /**
     * Lecture d'un message.
     *
     * @Route("/{uuid}", name="show")
     */
    public function InboxShow(Request $request, $uuid, InboxReplyRepository $inboxReplyRepository, InboxRepository $inboxRepository, Breadcrumbs $breadcrumbs, EntityManagerInterface $em)
    {
        $repos = $inboxRepository->findOneBy(['uuid' => $uuid]);

        $repos->setStatus('read');
        $em->persist($repos);
        $em->flush();

        $inboxReply = $inboxReplyRepository->findOneBy(['inbox' => $repos->getId()], ['createdAt' => 'DESC']);

        if ($this->getUser() && $repos->getSender()) {
            if ($repos->getSender() == $this->getUser()) {
                $user = $repos->getBook()->getUser();
            } else {
                $user = $repos->getSender()->getUser();
            }
            $title = $user->getFullname();
        } else {
            $title = $inboxReply->getName();
            $user = null;
        }

        if ($user) {
            $currentMessage = [
                'user' => [
                    'id' => $user->getId(),
                    'uuid' => $user->getUuid(),
                    'url' => $user->getLinkBook(),
                    'fullname' => $user->getFullname(),
                    'avatar' => $this->imagineServiceFilter->getUrlOfFilteredImage($user->getUser()->getAvatar(), 'avatar'),
                    'profession' => $user->getUser()->getProfession()->getTitle(),
                    'location' => $user->getUser()->getUser()->getAddress()->getFullAddress(),
                    'about' => $user->getUser()->getAbout(),
                    'social' => $user->getSocial(),
                    'images' => $user->getImages(),
                    'book' => [
                        'name' => $user->getBook()->getName(),
                    ],
                ],
                'uuid' => $repos->getUuid(),
                'date' => $repos->getCreatedAt(),
                'favorites' => $repos->getIsFavorites(),
                'report' => $repos->getIsReport(),
                'replies' => $repos->getInboxReplies(),
            ];
        } else {
            $currentMessage = [
                'user' => [
                    'id' => null,
                    'uuid' => null,
                    'url' => null,
                    'fullname' => $inboxReply->getName(),
                    'avatar' => new LetterAvatar($inboxReply->getName(), 'square', 64),
                    'profession' => 'Invité',
                    'location' => $inboxReply->getEmail(),
                    'phone' => $inboxReply->getPhone(),
                ],
                'uuid' => $repos->getUuid(),
                'date' => $repos->getCreatedAt(),
                'favorites' => $repos->getIsFavorites(),
                'report' => $repos->getIsReport(),
                'replies' => $repos->getInboxReplies(),
            ];
        }

        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans('Tous les messages'), $this->get('router')->generate('inbox_all'));
        $breadcrumbs->addItem($title);

        $inboxReply = new InboxReply();
        $form = $this->createForm(InboxReplyFormType::class, $inboxReply);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $add = new InboxReply();
            $add->setInbox($repos);
            $add->setUser($this->getUser());
            $add->setText($form->get('text')->getData());
            $add->setCreatedAt(new \DateTime());

            $em->persist($add);
            $em->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Votre message a bien été envoyé.'),
                ]);

            return $this->redirect($request->headers->get('referer'));
        }
        if ($currentMessage) {
            return $this->render('dashboard/inbox/show.html.twig', [
                'form' => $form->createView(),
                'title' => 'Avec ' . $title,
                'listInboxes' => $this->getListInboxes($inboxRepository),
                'currentMessage' => $currentMessage,
            ]);
        }
    }

    /**
     * Liste des messages reçus.
     *
     * @Route("/all/datas", name="inbox_all_data")
     */
    public function getInboxList(InboxRepository $inboxRepository)
    {
        $listInboxes = [];
        $inboxes = $inboxRepository->findBy(['book' => $this->getUser()], ['createdAt' => 'DESC']);
        foreach ($inboxes as $inbox) {
            $listInboxes[] = [
                'fullname' => $inbox->getSender->getFullname(),
                'message' => $inbox->getSender->getMessage(),
                'date' => $inbox->getCreatedAt(),
                'profession' => $inbox->getSender->getProfession(),
            ];
        }

        $datas = [
            'total' => count($inboxes),
            'rows' => $listInboxes,
        ];
        echo json_encode($datas);
        exit;
    }

    /**
     * dashboard_inbox_remove.
     *
     * @Route("/remove/{uuid}", name="inbox_remove")
     */
    public function InboxRemove($uuid, InboxRepository $inboxRepository, EntityManagerInterface $em)
    {
        $repos = $inboxRepository->findOneBy(['uuid' => $uuid, 'book' => $this->getUser()]);
        if ($repos) {
            $em->remove($repos);
            $em->flush();
        }

        return new Response(null, 204);
    }

    public function getSentListInboxes(InboxRepository $inboxRepository)
    {
        $listInboxes = [];
        $inboxes = $inboxRepository->findBy(['sender' => $this->getUser()], ['createdAt' => 'DESC']);
        foreach ($inboxes as $inbox) {
            $listInboxes[] = [
                'id' => $inbox->getId(),
                'uuid' => $inbox->getUuid(),
                //'fullname' => $inbox->getFullname(),
                'date' => $inbox->getCreatedAt(),
                //'message' => $inbox->getMessage(),
                //'avatar' => new LetterAvatar($inbox->getFullname(), 'square', 64),
                'status' => $inbox->getStatus(),
            ];
        }

        return $listInboxes;
    }

    /**
     * InboxCardUser.
     *
     * @Route("/card/user/{uuid}", name="card_user")
     */
    public function InboxCardUser($uuid, InboxRepository $inboxRepository, FollowRepository $followRepos, ImagesRepository $imagesRepository)
    {
        $repos = $inboxRepository->findOneBy(['uuid' => $uuid]);

        if ($repos->getSender() == $this->getUser()) {
            $user = $repos->getBook()->getUser();
        } else {
            $user = $repos->getSender()->getUser();
        }

        if (count($user->getGalleries()) <= 1) {
            $countGalleries = '<span class="fcounter">' . count($user->getGalleries()) . '</span> galerie';
        } else {
            $countGalleries = '<span class="fcounter">' . count($user->getGalleries()) . '</span> galeries';
        }

        if (count($user->getImages()) <= 1) {
            $countImages = '<span class="fcounter">' . count($user->getImages()) . '</span> publication';
        } else {
            $countImages = '<span class="fcounter">' . count($user->getImages()) . '</span> publications';
        }

        $filters = $this->get('twig')->getFunctions();
        $callable = $filters['uploaded_asset']->getCallable();

        $images = [];
        $listImages = $imagesRepository->findImagesLimit($user, $limit = 6);
        foreach ($listImages as $image) {
            $image_path = $this->imagineServiceFilter->getUrlOfFilteredImage($image->getImagePath(), 'thumbnail_square');
            $images[] = [
                'id' => $image->getId(),
                'title' => $image->getTitle(),
                'thumb' => $image_path,
                'path' => $callable($image->getImagePath()),
                'link' => $this->get('router')->generate('dashboard_media_show', ['gallerySlug' => $image->getGallery->getSlug(), 'mediaId' => $image->getId()]),
            ];
        }
        $user = [
            'images' => $images,
            'countFolders' => $countGalleries,
            'countMedias' => $countImages,
            'identity' => [
                'id' => $user->getId(),
                'followed' => ($followRepos->findOneBy(['user' => $this->getUser(), 'friend' => $user]) ? true : false),
                'url' => $user->getLinkBook(),
                'fullname' => $user->getFullname(),
                'username' => $user->getUsername(),
                'avatar' => $this->imagineServiceFilter->getUrlOfFilteredImage($user->getUser()->getAvatar(), 'avatar'),
                'experience' => $user->getUser()->getProfession()->getTitle(),
                'location' => $user->getUser()->getUser()->getAddress()->getFullAddress(),
                'about' => $user->getUser()->getAbout(),
                'social' => $user->getSocial(),
            ],
        ];
        echo json_encode($user);
        exit;
    }

    /**
     * Signalement d'une conversation.
     *
     * @Route("/show/{uuid}/report", name="make_report")
     */
    public function InboxMakeReport(InboxRepository $inboxRepository, Request $request, $uuid, EntityManagerInterface $em): Response
    {
        $inbox = $inboxRepository->findOneBy(['uuid' => $uuid, 'book' => $this->getUser()]);
        if ($inbox) {
            $inbox->setIsReport(true);
            $em->persist($inbox);
            $em->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Votre signalement a bien été enregistré, merci à vous. Nous mettons tout en œuvre pour régler cela au plus vite !'),
                ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Ajout du message dans les favoris.
     *
     * @Route("/show/{uuid}/favorites", name="make_favorites")
     */
    public function InboxMakeFavorites(InboxRepository $inboxRepository, Request $request, $uuid, EntityManagerInterface $em): Response
    {
        $inbox = $inboxRepository->findOneBy(['uuid' => $uuid, 'book' => $this->getUser()]);
        if ($inbox) {
            $inbox->setIsFavorites(true);
            $em->persist($inbox);
            $em->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Cette conversation a été ajouté dans vos favoris.'),
                ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Suppression du message des favoris.
     *
     * @Route("/show/{uuid}/remove/favorites", name="remove_favorites")
     */
    public function InboxRemoveFavorites(InboxRepository $inboxRepository, Request $request, $uuid, EntityManagerInterface $em): Response
    {
        $inbox = $inboxRepository->findOneBy(['uuid' => $uuid, 'book' => $this->getUser()]);
        if ($inbox) {
            $inbox->setIsFavorites(false);
            $em->persist($inbox);
            $em->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Cette conversation a été retiré dans vos favoris.'),
                ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    public function getListInboxes(InboxRepository $inboxRepository)
    {
        $listInboxes = [];
        $inboxes = $inboxRepository->findAllListActive($this->getUser());
        foreach ($inboxes as $inbox) {
            $lastMessage = $this->inboxReplyRepository->findOneBy(['inbox' => $inbox], ['createdAt' => 'DESC']);
            $senderGuest = $this->inboxReplyRepository->findOneBy(['inbox' => $inbox]);
            if ($inbox->getSender()) {
                if ($inbox->getSender() == $this->getUser()) {
                    $sender = $inbox->getBook();
                } else {
                    $sender = $inbox->getSender();
                }

                $listInboxes[] = [
                    'id' => $sender->getId(),
                    'uuid' => $inbox->getUuid(),
                    'fullname' => $sender->getFullname(),
                    'date' => $inbox->getCreatedAt(),
                    'message' => $lastMessage->getText(),
                    'avatar' => $this->imagineServiceFilter->getUrlOfFilteredImage($sender->getAvatar(), 'avatar'),
                    'status' => $inbox->getStatus(),
                    'favorites' => $inbox->getIsFavorites(),
                    'profession' => $sender->getProfession(),
                ];
            } else {
                $listInboxes[] = [
                    'id' => $inbox->getId(),
                    'uuid' => $inbox->getUuid(),
                    'fullname' => $senderGuest->getName(),
                    'date' => $inbox->getCreatedAt(),
                    'message' => $lastMessage->getText(),
                    'avatar' => new LetterAvatar($senderGuest->getName(), 'square', 64),
                    'status' => $inbox->getStatus(),
                    'favorites' => $inbox->getIsFavorites(),
                ];
            }
        }

        return $listInboxes;
    }
}
