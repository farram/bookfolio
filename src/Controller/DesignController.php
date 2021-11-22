<?php

namespace App\Controller;

use App\Entity\Guestbook;
use App\Form\GalleryPasswordFormType;
use App\Form\GuestBookFormType;
use App\Form\PortfolioContactFormType;
use App\Repository\BookRepository;
use App\Repository\DesignRepository;
use App\Repository\FollowRepository;
use App\Repository\GalleryRepository;
use App\Repository\GuestbookRepository;
use App\Repository\ImagesRepository;
use App\Repository\PageRepository;
use App\Repository\PlanRepository;
use App\Repository\StylePhotosRepository;
use App\Repository\UserRepository;
use App\Service\RandomFlashMessage;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class DesignController extends AbstractController
{
    public function __construct(BookRepository $bookRepository, UserRepository $userRepository, DesignRepository $designRepository, GalleryRepository $galleryRepository, ImagesRepository $imagesRepository, GuestbookRepository $guestBookRepository, PageRepository $pageRepository, TranslatorInterface $translator, RandomFlashMessage $randomFlashMessage)
    {
        $this->book = $bookRepository;
        $this->userRepository = $userRepository;
        $this->designRepository = $designRepository;
        $this->galleryRepository = $galleryRepository;
        $this->imagesRepository = $imagesRepository;
        $this->guestBookRepository = $guestBookRepository;
        $this->pageRepository = $pageRepository;
        $this->translator = $translator;
        $this->randomFlashMessage = $randomFlashMessage;
    }

    /**
     * @Route("/design", name="design")
     */
    public function index(): Response
    {
        return $this->render('design/index.html.twig');
    }

    /**
     * Récupération de la liste des designs.
     *
     * @Route("/dashboard/designs/json/{page}", name="json_designs")
     * @IsGranted("ROLE_USER")
     */
    public function all(Request $request, $page, DesignRepository $designRepository, PaginatorInterface $paginator, PlanRepository $planRepository)
    {
        $type = $request->query->get('type');
        $query = $designRepository->findAllActiveForVuejs($type);
        $pageSize = '6';
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $designs = [];

        $filters = $this->get('twig')->getFunctions();
        $callable = $filters['asset']->getCallable();
        foreach ($paginator as $design) {
            $plan = [];
            foreach ($design->getPlan() as $value) {
                $plan[] = [
                    'name' => $value->getPlanName(),
                    'slug' => $value->getSlug(),
                    'badgeColor' => $value->getBadgeColor(),
                ];
            }

            $designs[] = [
                'id' => $design->getId(),
                'plan' => $plan,
                'image' => $callable('assets/tpl/' . $design->getSlug() . '/preview.jpeg'),
                'title' => $design->getTitle(),
                'link' => [
                    'preview' => $this->get('router')->generate('template_preview', ['slug' => $design->getSlug()]),
                    'change_plan' => $this->get('router')->generate('pricing_all'),
                    'choose' => $this->get('router')->generate('manage_book_design_choice', ['id' => $design->getId()]),
                ],
                'user' => [
                    'hasActiveSubscription' => $this->getUser()->hasActiveSubscription(),
                    'design' => [
                        'id' => $this->getUser()->getBook()->getDesign()->getId(),
                    ],
                ],
            ];
        }

        $plansQuery = $planRepository->findAll();
        $filterList = [];
        foreach ($plansQuery as $value) {
            $filterList[] = [
                'id' => $value->getId(),
                'title' => $value->getPlanName(),
            ];
        }

        $datas = [
            'items' => $designs,
            'totalPages' => $pagesCount,
            'sortList' => '',
            'filterList' => $filterList,
        ];

        return new JsonResponse($datas);
        exit;
    }

    /**
     * Prévisualisation des templates.
     *
     * @Route("/design/{slug}/", name="design")
     */
    public function design($slug, DesignRepository $designRepository, Request $request, UserRepository $userRepository)
    {
        $design = $designRepository->findOneBy(['slug' => $slug]);
        $user = $userRepository->findOneBy(['isDemo' => true]);
        $book = $this->book->findOneBy(['name' => $user->getBook()->getName()]);

        return $this->render('portfolio/' . $design->getSlug() . '/index.html.twig', [
            'design' => $design->getSlug(),
            'book' => $book,
            'galleries' => $this->getGalleries($book),
            'pages' => $this->getPages($book),
            'iamFollow' => true,
            'showGallery' => false,
            'getMediasHideHome' => $this->getMediasHideHome($book),
        ]);
    }

    /**
     * Prévisualisation des templates.
     *
     * @Route("/design/{slug}/preview", name="template_preview")
     */
    public function preview($slug)
    {
        $design = $this->designRepository->findOneBy(['slug' => $slug]);

        return $this->render('design/index.html.twig', [
            'slug' => $slug,
            'design' => $design,
            'showGallery' => false,
        ]);
    }

    /**
     * @Route("/design/{slug}/preview/home", name="template_preview_index")
     */
    public function previewIndex($slug, ImagesRepository $imagesRepository): Response
    {
        $design = $this->designRepository->findOneBy(['slug' => $slug]);
        $user = $this->userRepository->findOneBy(['isDemo' => true]);
        $book = $this->book->findOneBy(['name' => $user->getBook()->getName()]);

        return $this->render('portfolio/' . $design->getSlug() . '/index.html.twig', [
            'design' => $design->getSlug(),
            'book' => $book,
            'galleries' => '',
            'pages' => $this->getPages($book),
            'iamFollow' => '',
            'showGallery' => false,
            'getMediasHideHome' => '',
        ]);
    }

    /**
     * À propos.
     *
     * @Route("/design/{slug}/preview/about", name="template_preview_about")
     */
    public function BookAbout($slug, BookRepository $book, StylePhotosRepository $styleRepos, FollowRepository $follow)
    {
        $design = $this->designRepository->findOneBy(['slug' => $slug]);
        $user = $this->userRepository->findOneBy(['isDemo' => true]);
        $book = $this->book->findOneBy(['name' => $user->getBook()->getName()]);

        $followBack = $follow->findBy(['friend' => $book->getUser()]);
        // Style de photo

        $stylePhoto = '';
        if ($book->getStylePhotos()) {
            foreach ($book->getStylePhotos() as $i => $k) {
                $name = $styleRepos->find($k);
                if ($name) {
                    $stylePhoto .= $name->getTitle() . ', ';
                }
            }
            $stylePhoto = rtrim($stylePhoto, ', ');
        }

        return $this->render('portfolio/' . $design->getSlug() . '/about.html.twig', [
            'design' => $design->getSlug(),
            'book' => $book,
            'stylePhoto' => $stylePhoto,
            'galleries' => '',
            'pages' => $this->getPages($book),
            'followBack' => $followBack,
            'iamFollow' => '',
        ]);
    }

    /**
     * Galeries.
     *
     * @Route("/design/{slug}/preview/galleries", name="template_preview_galleries")
     */
    public function BookGalleries($slug)
    {
        $design = $this->designRepository->findOneBy(['slug' => $slug]);
        $user = $this->userRepository->findOneBy(['isDemo' => true]);
        $book = $this->book->findOneBy(['name' => $user->getBook()->getName()]);

        return $this->render('portfolio/' . $design->getSlug() . '/galleries.html.twig', [
            'design' => $design->getSlug(),
            'book' => $book,
            'galleries' => $this->getGalleries($book),
            'pages' => $this->getPages($book),
            'iamFollow' => '',
        ]);
    }

    /**
     * Le contenu d'une galerie.
     *
     * @Route("/design/{slug_design}/preview/galeries/{slug}", name="template_preview_gallery_show")
     */
    public function BookGalleryShow(ImagesRepository $imagesRepository, $page = 1, PaginatorInterface $paginator, Request $request, $slug_design, $slug, PasswordHasherFactoryInterface $passwordHasherFactoryInterface)
    {
        $design = $this->designRepository->findOneBy(['slug' => $slug_design]);
        $user = $this->userRepository->findOneBy(['isDemo' => true]);
        $book = $this->book->findOneBy(['name' => $user->getBook()->getName()]);

        $gallery = $this->galleryRepository->findOneBy(['user' => $book->getUser(), 'slug' => $slug, 'isActive' => true]);
        if (!$gallery) {
            return $this->redirectToRoute('template_preview_index', ['slug' => $slug_design]);
        }

        $images = $imagesRepository->findBy(['user' => $book->getUser(), 'gallery' => $gallery, 'isVisible' => true], ['position' => 'ASC']);

        // Si la galerie est protégée par un mot de passe
        // On affiche le formulaire
        $form = $this->createForm(GalleryPasswordFormType::class);
        $form->handleRequest($request);
        if (true == $gallery->getIsProtect()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $plainPassword = $form->get('password')->getData();
                $validPassword = password_verify(
                    $plainPassword,
                    $gallery->getPassword(),
                );
                if ($validPassword) {
                    $session = new Session();
                    $session->set('sessionUnlockedGalleries', [
                        $gallery->getId(),
                    ]);
                }

                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->render('portfolio/' . $design->getSlug() . '/gallery_show.html.twig', [
            'design' => $design->getSlug(), // Path du design
            'book' => $book, // les infos du book concerné
            'galleries' => $this->getGalleries($book), // La liste des gallerie. Affichée en sous menu du le header du portfolio
            'gallery' => $gallery, // La galerie courante
            'form' => $form->createView(), // Formulaire de mot de passe
            'pages' => $this->getPages($book), // Liste des pages. Affichée dans le menu du header du portfolio
            'iamFollow' => '', // Afficher dans le footer. Possibilité de follow ou unfollow
            'images' => $images, // Liste des images de la galerie
        ]);
    }

    /**
     * Vidéos.
     *
     * @Route("/design/{slug}/preview/videos", name="template_preview_video")
     */
    public function BookVideos($slug, Request $request, PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        $design = $this->designRepository->findOneBy(['slug' => $slug]);
        $user = $this->userRepository->findOneBy(['isDemo' => true]);
        $book = $this->book->findOneBy(['name' => $user->getBook()->getName()]);

        if (count($book->getUser()->getVideos()) > 0) {
            $query = $this->get('doctrine')
                ->getManager()
                ->getRepository('App:Video')
                ->findByUser($user, ['createdAt' => 'DESC']);

            $videos = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                $this->getParameter('PerPage')
            );

            return $this->render('portfolio/' . $design->getSlug() . '/videos.html.twig', [
                'design' => $design->getSlug(),
                'book' => $book,
                'galleries' => $this->getGalleries($book),
                'pages' => $this->getPages($book),
                'iamFollow' => '',
                'videos' => $videos,
            ]);
        } else {
            return $this->redirectToRoute('template_preview_index', ['slug' => $slug]);
        }
    }

    /**
     * Commentaires.
     *
     * @Route("/design/{slug}/preview/commentaires", name="template_preview_guestbook")
     */
    public function BookGuestbook(Request $request, $slug, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $design = $this->designRepository->findOneBy(['slug' => $slug]);
        $user = $this->userRepository->findOneBy(['isDemo' => true]);
        $book = $this->book->findOneBy(['name' => $user->getBook()->getName()]);

        if (true == $book->getAllowComments()) {
            $form = $this->createForm(GuestBookFormType::class);
            $form->handleRequest($request);

            $comments = $paginator->paginate(
                $this->getGuestBook($book),
                $request->query->getInt('page', 1),
                $this->getParameter('PerPage')
            );

            if ($form->isSubmitted() && $form->isValid()) {
                $guestbook = new Guestbook();
                $guestbook->setUser($book->getUser());
                $guestbook->setEmail($form->get('email')->getData());
                $guestbook->setContent($form->get('content')->getData());
                $guestbook->setCreatedAt(new \DateTime('now'));
                $guestbook->setIpAddress($request->getClientIp());
                $guestbook->setAuthor($form->get('Author')->getData());
                $guestbook->setLocation($form->get('location')->getData());
                $guestbook->setWebsite($form->get('website')->getData());
                $guestbook->setIsActive(false);

                $em->persist($guestbook);
                $em->flush();

                // On envoi un mail à l'utillisateur pour l'informer d'un nouveau commentaire.

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => 'C\'est envoyé !',
                        'message' => 'Votre commentaire est en attente de validation auprès de ' . $book->getUser()->getFirstname() . '.',
                    ]);

                return $this->redirect($request->headers->get('referer'));
            }

            return $this->render('portfolio/' . $design->getSlug() . '/comments.html.twig', [
                'design' => $design->getSlug(),
                'book' => $book,
                'galleries' => $this->getGalleries($book),
                'pages' => $this->getPages($book),
                'comments' => $comments,
                'form' => $form->createView(),
                'iamFollow' => '',
            ]);
        } else {
            return $this->redirectToRoute('portfolio_index', ['slug' => $slug]);
        }
    }

    /**
     * Contact.
     *
     * @Route("/design/{slug}/preview/contact", name="template_preview_contact")
     */
    public function BookContact(Request $request, $slug, EntityManagerInterface $em)
    {
        $design = $this->designRepository->findOneBy(['slug' => $slug]);
        $user = $this->userRepository->findOneBy(['isDemo' => true]);
        $book = $this->book->findOneBy(['name' => $user->getBook()->getName()]);

        if (true == $book->getShowContact()) {
            $form = $this->createForm(PortfolioContactFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => 'Cool !',
                        'message' => 'Ceci est une démo mais faisant comme si le message a été envoyé à ' . $book->getUser()->getFullname() . ' =)',
                    ]);

                return $this->redirect($request->headers->get('referer'));
            }

            return $this->render('portfolio/' . $design->getSlug() . '/contact.html.twig', [
                'design' => $design->getSlug(),
                'book' => $book,
                'galleries' => $this->getGalleries($book),
                'pages' => $this->getPages($book),
                'iamFollow' => '',
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('template_preview_index', ['slug' => $slug]);
        }
    }

    /**
     * Page.
     *
     * @Route("/design/{slug_page}/preview/{slug}", name="template_preview_page")
     */
    public function BookPage($slug_page, $slug, PageRepository $page)
    {
        $design = $this->designRepository->findOneBy(['slug' => $slug_page]);
        $user = $this->userRepository->findOneBy(['isDemo' => true]);
        $book = $this->book->findOneBy(['name' => $user->getBook()->getName()]);

        $page = $page->findOneBy(['slug' => $slug, 'user' => $book->getUser(), 'isActive' => true]);

        return $this->render('portfolio/' . $design->getSlug() . '/page.html.twig', [
            'design' => $design->getSlug(),
            'book' => $book,
            'galleries' => $this->getGalleries($book),
            'pages' => $this->getPages($book),
            'page' => $page,
            'iamFollow' => '',
        ]);
    }

    public function getGuestBook($book)
    {
        $comments = $this->guestBookRepository->findBy(['user' => $book->getUser(), 'isActive' => true], ['createdAt' => 'DESC']);
        $items = [];
        foreach ($comments as $comment) {
            $user = $this->userRepository->findOneBy(['email' => $comment->getEmail()]);
            $items[] = [
                'list' => $comment,
                'user' => $user,
            ];
        }

        return $items;
    }

    public function getPages($book)
    {
        return $this->pageRepository->findBy(['user' => $book->getUser(), 'isActive' => true], ['createdAt' => 'ASC']);
    }

    public function getGalleries($book)
    {
        $galleries = $this->galleryRepository->findWithImages($book);

        $items = [];
        foreach ($galleries as $gallery) {
            $items[] = [
                'list' => $gallery,
                'cover' => $this->getLastPublishedImage($gallery->getId()),
            ];
        }

        return $items;
    }

    protected function getLastPublishedImage($id)
    {
        return $this->imagesRepository->findLastFromGallery($id);
    }

    public function getMediasHideHome($book)
    {
        $query = $this->imagesRepository->findBy(['user' => $book->getUser(), 'isVisible' => true, 'isGallery' => true]);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Choix du design.
     *
     * @Route("/dashboard/manage/book/design", name="manage_book_design")
     */
    public function ManageBookDesign(Breadcrumbs $breadcrumbs, Request $request, DesignRepository $designRepository)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Designs');

        // On récupère tous les design actif
        $designs = $designRepository->findBy(['isActive' => 1], ['position' => 'DESC']);
        $current_design = $designRepository->find($this->getUser()->getBook()->getDesign());

        return $this->render('dashboard/manage/book/designs.html.twig', [
            'designs' => $designs,
            'current_design' => $current_design,
            'title' => $this->translator->trans('Configuration de votre book'),
        ]);
    }

    /**
     * Validation choix du design.
     *
     * @Route("/dashboard/manage/book/design/{id}/choice", name="manage_book_design_choice")
     */
    public function ManageBookDesignChoice(Request $request, BookRepository $bookRepository, $id, DesignRepository $designRepository, EntityManagerInterface $em)
    {
        $design = $designRepository->find($id);
        // Si le design existe, on poursuit
        if ($design) {
            // Si ce design est destiné aux pro ou awesome
            if ('starter' != $design->getSlug()) {
                // Si l'utilisateur n'est pas free, on stop.
                if (!$this->getUser()->hasActiveSubscription()) {
                    $this->get('session')->getFlashBag()
                        ->add('notice', [
                            'type' => 'warning',
                            'title' => 'Accès restreint',
                            'message' => $this->translator->trans("Vous devez disposer d'une formule Awesome ou Pro pour utiliser ce design."),
                            // Ce design est destiné uniquement aux artistes ayant une formule Pro ou Awesome. Changez votre offre pour utiliser ce design.
                        ]);
                } else {
                    $user = $this->getUser();
                    $book = $bookRepository->findOneBy(['user' => $this->getUser()]);
                    $book->setDesign($design);
                    $user->setUpdatedAt(new DateTime('now'));
                    $em->persist($book);
                    //On mets aussi à jour la date de mise à jour du book dans l'entité User
                    $em->persist($user);
                    $em->flush();
                    $this->get('session')->getFlashBag()
                        ->add('notice', [
                            'type' => 'success',
                            'title' => $this->randomFlashMessage->getTitle(),
                            'message' => $this->translator->trans('Le design de votre book a bien été changé.'),
                        ]);
                }
            } else {
                $user = $this->getUser();
                $book = $bookRepository->findOneBy(['user' => $this->getUser()]);
                $book->setDesign($design);
                $user->setUpdatedAt(new DateTime('now'));
                $em->persist($book);
                //On mets aussi à jour la date de mise à jour du book dans l'entité User
                $em->persist($user);
                $em->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->randomFlashMessage->getTitle(),
                        'message' => $this->translator->trans('Le design de votre book a bien été changé.'),
                    ]);
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
