<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\AnnoncesComment;
use App\Form\AnnonceFormCommentType;
use App\Form\AnnonceFormType;
use App\Repository\AnnoncesCommentRepository;
use App\Repository\AnnoncesRepository;
use App\Repository\ImagesRepository;
use App\Service\AwsImageService;
use App\Service\Mailer;
use App\Service\RandomFlashMessage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Bundle\TimeBundle\DateTimeFormatter;
use Knp\Component\Pager\PaginatorInterface;
use Liip\ImagineBundle\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Urodoz\Truncate\TruncateService;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class AnnoncesController extends AbstractController
{
    public function __construct(
        TranslatorInterface $translator,
        RandomFlashMessage $randomFlashMessage,
        Mailer $mailer,
        AwsImageService $awsImageService
    ) {
        $this->translator = $translator;
        $this->randomFlashMessage = $randomFlashMessage;
        $this->mailer = $mailer;
        $this->awsImageService = $awsImageService;
    }

    /**
     * @Route("/annonces", name="front_annonces_index", methods={"GET"})
     */
    public function index(AnnoncesRepository $annoncesRepository, Request $request, PaginatorInterface $paginator)
    {
        $annonces = $annoncesRepository->findActive();

        // Récupération des vidéos de l'utilisateur
        $query = $annoncesRepository->findActive();

        $annonces = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->getParameter('PerPage')
        );

        return $this->render('index/annonces/all.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    /**
     * Visualisation de l'annonce front.
     *
     * @Route("/annonce/{slug}", name="front_annonces_show")
     */
    public function AnnonceShow($slug, AnnoncesRepository $annoncesRepository, Request $request, AnnoncesCommentRepository $annoncesCommentRepository, EntityManagerInterface $em)
    {
        $annonce = $annoncesRepository->findOneBy(['slug' => $slug, 'isActive' => true]);
        if ($annonce) {
            $annonceComment = new AnnoncesComment();
            $form = $this->createForm(AnnonceFormCommentType::class, $annonceComment);
            $form->handleRequest($request);
            $reactions = $annoncesCommentRepository->findBy(['annonce' => $annonce, 'isActive' => 1], ['createdAt' => 'DESC']);

            return $this->render('index/annonces/show.html.twig', [
                'annonce' => $annonce,
                'reactions' => $reactions,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * Ajout de la réaction.
     *
     * @Route("/annonce/{slug}/reaction/add", name="front_add_reaction_annonce", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function AddReactionAnnonce($slug, Request $request, AnnoncesRepository $annoncesRepository, EntityManagerInterface $em): Response
    {
        $annonce = $annoncesRepository->findOneBy(['slug' => $slug, 'isActive' => true]);
        if ($annonce) {
            $annonceComment = new AnnoncesComment();
            $form = $this->createForm(AnnonceFormCommentType::class, $annonceComment);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $annonceComment->setAnnonce($annonce);
                $annonceComment->setUser($this->getUser());
                $annonceComment->setComment($form->get('comment')->getData());
                $annonceComment->setCreatedAt(new \DateTime('now'));
                $annonceComment->setIsActive(true);
                $annonceComment->setUpdatedAt(new \DateTime('now'));
                $em->persist($annonceComment);
                $em->flush();

                // On envoi un mail à l'auteur
                // On vérifie que le destinataire n'est pas l'auteur
                if ($annonce->getUser() != $this->getUser()) {
                    $this->mailer->sendEmailReactionAnnonce($annonceComment);
                }

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->randomFlashMessage->getTitle(),
                        'message' => $this->translator->trans('Votre réaction a bien a bien été prise en compte'),
                    ]);

                return $this->redirect($request->headers->get('referer'));
            }
        } else {
            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * Récupération des 5 dernières annonces publiées.
     *
     * @Route("/dashboard/annonce/last/{page}", name="last_post", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function jsonLast(AnnoncesRepository $annoncesRepository, DateTimeFormatter $dateTimeFormatter, FilterService $filterService)
    {
        $limit = 5;
        $now = new \DateTime();

        $listAnnonces = $annoncesRepository->findBy(['isActive' => 1], ['createdAt' => 'DESC'], $limit);
        $annonces = [];
        foreach ($listAnnonces as $annonce) {
            $annonces[] = [
                'date' => $dateTimeFormatter->formatDiff($annonce->getCreatedAt(), $now),
                'author' => '<span class="text--primary">'.$annonce->getUser()->getFullname().'</span> &mdash; '.$annonce->getUser()->getProfession()->getTitle(),
                'title' => $annonce->getTitle(),
                'link' => '',
                'user' => [
                    'avatar' => $this->awsImageService->getPathAvatar($annonce->getUser()->getAvatar()),
                    'fullname' => $annonce->getUser()->getFullname(),
                ],
            ];
        }
        $datas = [
            'annonces' => $annonces,
            'link' => $this->get('router')->generate('annonce_all'),
        ];

        echo json_encode($datas);
        exit;
    }

    /**
     * Annonces.
     *
     * @Route("/dashboard/annonces/short/{page}", name="short_annonces")
     * @IsGranted("ROLE_USER")
     */
    public function ShortAnnonces(ImagesRepository $imagesRepository, AnnoncesRepository $annoncesRepository, FilterService $filterService, DateTimeFormatter $dateTimeFormatter)
    {
        if ($this->getUser()->hasActiveSubscription()) {
            $suggestFollowers = $imagesRepository->findMyFeed($this->getUser());
            $paginatorSuggestFollowers = new Paginator($suggestFollowers);
            $totalItemsSuggestFollowers = count($paginatorSuggestFollowers);

            $limit = 5;
            $listAnnonces = $annoncesRepository->findBy(['isActive' => true], ['createdAt' => 'DESC'], $limit);
            $annonces = [];

            $now = new \DateTime();

            foreach ($listAnnonces as $annonce) {
                $annonces[] = [
                    'id' => $annonce->getId(),
                    'title' => $annonce->getTitle(),
                    'date' => $dateTimeFormatter->formatDiff($annonce->getCreatedAt(), $now),
                    'url' => '',
                    'user' => [
                        'avatar' => $this->awsImageService->getPathAvatar($annonce->getUser()->getAvatar()),
                        'fullname' => $annonce->getUser()->getFullname(),
                        'url' => $this->get('router')->generate('portfolio_index', ['name' => $annonce->getUser()->getBook()->getName()]),
                    ],
                ];
            }
            $datas = [
                'totalItemsSuggestFollowers' => $totalItemsSuggestFollowers,
                'annonces' => $annonces,
            ];

            echo json_encode($datas);
            exit;
        }
    }

    /**
     * Dashboard : Toutes les annonces de l'utilisateur.
     *
     * @Route("/dashboard/annonce/all", name="annonce_all")
     * @IsGranted("ROLE_USER")
     */
    public function all(Breadcrumbs $breadcrumbs, AnnoncesRepository $annoncesRepository)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Toutes vos annonces');
        $annonces = $annoncesRepository->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('dashboard/annonces/all.html.twig', [
            'annonces' => $annonces,
            'title' => $this->translator->trans('Annonces'),
        ]);
    }

    /**
     * Dashboard : Ajout d'annonces.
     *
     * @Route("/dashboard/annonce/add", name="annonce_add")
     * @IsGranted("ROLE_USER")
     */
    public function AnnoncesAdd(Breadcrumbs $breadcrumbs, Request $request)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Ajouter une annonce');

        $annonce = new Annonces();
        $form = $this->createForm(AnnonceFormType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setUser($this->getUser());
            $annonce->setIsActive(false);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Votre annonce a bien été créée'),
                ]);

            return $this->redirectToRoute('annonce_all');
        }

        return $this->render('dashboard/annonces/add.html.twig', [
            'title' => $this->translator->trans('Annonces'),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edition d'annonce.
     *
     * @Route("/dashboard/annonce/{slug}/edit", name="annonce_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function AnnoncesEdit(Breadcrumbs $breadcrumbs, Request $request, $slug): Response
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository('App:Annonces')->findOneBy(['slug' => $slug, 'user' => $this->getUser()]);
        if (!$annonce) {
            return $this->redirectToRoute('annonce_all');
        }

        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Toutes vos annonces', $this->get('router')->generate('annonce_all'));
        $breadcrumbs->addItem('Modifier mon annonce');

        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Votre annonce a bien été mise à jour'),
                ]);

            return $this->redirectToRoute('annonce_all');
        }

        return $this->render('dashboard/annonces/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
            'title' => $this->translator->trans('Annonces'),
        ]);
    }

    /**
     * Suppression d'annonce.
     *
     * @Route("/dashboard/annonce/{id}/delete", name="annonce_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function AnnoncesDelete(Breadcrumbs $breadcrumbs, Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository('App:Annonces')->findOneBy(['id' => $id, 'user' => $this->getUser()]);
        if (!$annonce) {
            return $this->redirectToRoute('annonce_all');
        }

        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annonce);
            $entityManager->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Votre annonce a bien été supprimées'),
                ]);
        }

        return $this->redirectToRoute('annonce_all');
    }

    /**
     * Suppression d'une reaction.
     *
     * @Route("/dashboard/annonce/{annonceId}/reaction/{id}/delete", name="reaction_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function ReactionDelete($annonceId, Request $request, $id, AnnoncesRepository $annoncesRepository, AnnoncesCommentRepository $annoncesCommentRepository): Response
    {
        $annonce = $annoncesRepository->findOneBy(['id' => $annonceId, 'user' => $this->getUser()]);
        if (!$annonce) {
            return $this->redirectToRoute('annonce_all');
        }

        $reaction = $annoncesCommentRepository->findOneBy(['id' => $id, 'annonce' => $annonce]);

        if ($this->isCsrfTokenValid('delete'.$reaction->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reaction);
            $entityManager->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('La réaction a bien été supprimée'),
                ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Visualisation de l'annonce.
     *
     * @Route("/dashboard/annonce/{slug}", name="annonce_show")
     * @IsGranted("ROLE_USER")
     */
    public function AnnoncesShow(Breadcrumbs $breadcrumbs, $slug, AnnoncesCommentRepository $annoncesCommentRepository, Request $request): Response
    {
        $truncateService = new TruncateService();
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository('App:Annonces')->findOneBy(['slug' => $slug, 'user' => $this->getUser()]);
        if (!$annonce) {
            return $this->redirectToRoute('annonce_all');
        }

        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Toutes vos annonces', $this->get('router')->generate('annonce_all'));
        $breadcrumbs->addItem($truncateService->truncate($annonce->getTitle(), 30));

        $annonceComment = new AnnoncesComment();
        $form = $this->createForm(AnnonceFormCommentType::class, $annonceComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonceComment->setAnnonce($annonce);
            $annonceComment->setUser($this->getUser());
            $annonceComment->setComment($form->get('comment')->getData());
            $annonceComment->setCreatedAt(new \DateTime('now'));
            $annonceComment->setIsActive(true);
            $annonceComment->setUpdatedAt(new \DateTime('now'));
            $em->persist($annonceComment);
            $em->flush();

            // On envoi un mail à l'auteur
            // On vérifie que le destinataire n'est pas l'auteur
            if ($annonce->getUser() != $this->getUser()) {
                $this->mailer->sendEmailReactionAnnonce($annonceComment);
            }

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Votre réaction a bien a bien été prise en compte'),
                ]);

            return $this->redirect($request->headers->get('referer'));
        }

        $reactions = $annoncesCommentRepository->findBy(['annonce' => $annonce, 'isActive' => 1], ['createdAt' => 'DESC']);

        return $this->render('dashboard/annonces/show.html.twig', [
            'annonce' => $annonce,
            'reactions' => $reactions,
            'form' => $form->createView(),
            'title' => $this->translator->trans('Annonces'),
        ]);
    }
}
