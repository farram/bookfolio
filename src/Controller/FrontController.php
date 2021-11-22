<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AbusType;
use App\Form\AvisFormType;
use App\Form\ContactType;
use App\Form\SuggestionBoxType;
use App\Repository\AvantagesRepository;
use App\Repository\AvisRepository;
use App\Repository\DesignRepository;
use App\Repository\ImageCoverRepository;
use App\Repository\ImageHeartstrokeRepository;
use App\Repository\ReleaseNotesRepository;
use App\Repository\UserRepository;
use App\Service\AwsImageService;
use App\Service\Mailer;
use App\Service\RandomFlashMessage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Liip\ImagineBundle\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Urodoz\Truncate\TruncateService;

class FrontController extends AbstractController
{
    public function __construct(
        FilterService $filterService,
        TranslatorInterface $translator,
        RandomFlashMessage $randomFlashMessage,
        Mailer $mailer,
        AwsImageService $awsImageService
    ) {
        $this->translator = $translator;
        $this->filterService = $filterService;
        $this->randomFlashMessage = $randomFlashMessage;
        $this->mailer = $mailer;
        $this->awsImageService = $awsImageService;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(ImageCoverRepository $imageCoverRepository, UserRepository $userRepository, AvisRepository $avisRepository, ImageHeartstrokeRepository $imageHeartstrokeRepository)
    {
        $truncateService = new TruncateService();

        $filters = $this->get('twig')->getFunctions();
        $callable = $filters['uploaded_asset']->getCallable();
        $coverImage = $imageCoverRepository->findOneBy(['isActive' => true]);

        if ($coverImage) {
            $cover = [
                'path' => $callable($coverImage->getImage()->getImagePath()),
                'user' => $coverImage->getImage()->getUser(),
            ];
        } else {
            $cover = null;
        }

        $users = [];
        $users_datas = $userRepository->findUsersForHome(4);

        foreach ($users_datas as $user) {
            if (count($user->getGalleries()) <= 1) {
                $countGalleries = '<span class="fcounter">'.count($user->getGalleries()).'</span> galerie';
            } else {
                $countGalleries = '<span class="fcounter">'.count($user->getGalleries()).'</span> galeries';
            }

            if (count($user->getImages()) <= 1) {
                $countImages = '<span class="fcounter">'.count($user->getImages()).'</span> publication';
            } else {
                $countImages = '<span class="fcounter">'.count($user->getImages()).'</span> publications';
            }

            $users[] = [
                'book' => $user->getBook(),
                'url' => $this->get('router')->generate('portfolio_index', ['name' => $user->getBook()->getName()]),
                'avatar' => $this->awsImageService->getPathAvatar($user->getAvatar()),
                'fullname' => ($user->getFullname() ? '' : $user->getFullname()),
                'username' => $user->getUsername(),
                'profession' => $user->getProfession()->getTitle(),
                'countImages' => $countImages,
                'countGalleries' => $countGalleries,
                'address' => $user->getAddress(),
            ];
        }

        // Testimonial
        $testimonials = [];
        $testimonials_data = $avisRepository->findByLimit(3);
        if ($testimonials_data) {
            foreach ($testimonials_data as $value) {
                $testimonials[] = [
                    'content' => $truncateService->truncate($value->getMessage(), 100),
                    'user' => [
                        'fullname' => $value->getUser()->getFullname(),
                        'book' => $value->getUser()->getBook(),
                    ],
                ];
            }
        }

        return $this->render('index/index.html.twig', [
            'cover' => $cover,
            'users' => $users,
            'testimonials' => $testimonials,
        ]);
    }

    /**
     * @Route("/images/home/{page}", name="front_images_home")
     */
    public function ImagesHome($page, ImageHeartstrokeRepository $imageHeartstrokeRepository, FilterService $filterService): Response
    {
        $query = $imageHeartstrokeRepository->findLimit(6);
        $filters = $this->get('twig')->getFunctions();
        $callable = $filters['uploaded_asset']->getCallable();

        $images = [];
        foreach ($query as $heartstroke) {
            $images[] = [
                'id' => $heartstroke->getImage()->getid(),
                'title' => $heartstroke->getImage()->getTitle(),
                'thumb_path' => $this->awsImageService->getPathImageProvider($heartstroke->getImage()->getImagePath(), 'thumb_large'),
                'path' => $callable($heartstroke->getImage()->getImagePath()),
                'user' => [
                    'fullname' => $heartstroke->getImage()->getUser()->getFullname(),
                    'book' => $heartstroke->getImage()->getUser()->getBook(),
                ],
            ];
        }

        $datas = [
            'images' => $images,
        ];

        echo json_encode($images);
        exit;
    }

    /**
     * Inspiration.
     *
     * @Route("/images/inspiration/{page}", name="image_inspiration")
     */
    public function ImagesInspiration($page, ImageHeartstrokeRepository $imageHeartstrokeRepository, FilterService $filterService): Response
    {
        $query = $imageHeartstrokeRepository->findNoLimit();

        $pageSize = '20';
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $filters = $this->get('twig')->getFunctions();
        $callable = $filters['uploaded_asset']->getCallable();

        $images = [];
        foreach ($paginator as $heartstroke) {
            $images[] = [
                'id' => $heartstroke->getImage()->getid(),
                'title' => $heartstroke->getImage()->getTitle(),
                'thumb_path' => $this->awsImageService->getPathImageProvider($heartstroke->getImage()->getImagePath(), 'thumb_large'),
                'path' => $callable($heartstroke->getImage()->getImagePath()),
                'user' => [
                    'fullname' => $heartstroke->getImage()->getUser()->getFullname(),
                    'book' => $this->get('router')->generate('portfolio_index', ['name' => $heartstroke->getImage()->getUser()->getBook()->getName()]),
                ],
            ];
        }

        $datas = [
            'images' => $images,
            'totalPages' => $pagesCount,
        ];

        echo json_encode($datas);
        exit;
    }

    /**
     * @Route("/inspiration", name="inspiration")
     */
    public function inspiration()
    {
        return $this->render('index/inspiration.html.twig', []);
    }

    /**
     * @Route("/templates", name="designs")
     */
    public function designs(DesignRepository $designRepository)
    {
        $designs = $designRepository->findByActive();

        return $this->render('index/templates.html.twig', [
            'designs' => $designs,
        ]);
    }

    /**
     * @Route("/terms", name="cgu")
     */
    public function cgu()
    {
        return $this->render('index/cgu.html.twig', []);
    }

    /**
     * @Route("/politicy", name="politicy")
     */
    public function politicy()
    {
        return $this->render('index/politicy.html.twig', []);
    }

    /**
     * @Route("/work", name="work")
     */
    public function work()
    {
        return $this->render('index/work.html.twig', []);
    }

    /**
     * @Route("/abus", name="abus")
     */
    public function abus(Request $request, NotifierInterface $notifier)
    {
        $form = $this->createForm(AbusType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $notification = (new Notification('Abus - Bookfolio', ['email']))
                ->content('Signalement de '.$form->get('book')->getData().' - '.$form->get('email')->getData().' ')
                ->importance(Notification::IMPORTANCE_HIGH);
            $notifier->send($notification, new Recipient('info@book-folio.fr'));

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Message envoyé ! Merci pour votre signalement.'),
                ]);

            return $this->redirectToRoute('contact');
        }

        return $this->render('index/abus.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/suggestion-box", name="suggestion_box")
     */
    public function suggestionBox(Request $request, NotifierInterface $notifier)
    {
        $form = $this->createForm(SuggestionBoxType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $notification = (new Notification('Suggestion - Bookfolio', ['email']))
                ->content('Suggestion de '.$form->get('email')->getData().' ')
                ->importance(Notification::IMPORTANCE_HIGH);
            $notifier->send($notification, new Recipient('info@book-folio.fr'));

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Message envoyé ! Merci pour votre suggestion.'),
                ]);

            return $this->redirectToRoute('suggestion_box');
        }

        return $this->render('index/suggestion_box.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/avis", name="avis")
     */
    public function avis(AvisRepository $avisRepository, Request $request, PaginatorInterface $paginator)
    {
        $query = $avisRepository->findActiveByQuery();

        $avis = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->getParameter('PerPage')
        );

        return $this->render('index/avis/all.html.twig', [
            'avis' => $avis,
        ]);
    }

    /**
     * @Route("/avis/add", name="avis_add")
     * @IsGranted("ROLE_USER")
     */
    public function addAvis(Request $request, EntityManagerInterface $em, NotifierInterface $notifier)
    {
        $avis = new Avis();
        $form = $this->createForm(AvisFormType::class, $avis);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setUser($this->getUser());
            $avis->setMessage($form->get('message')->getData());
            $avis->setCreatedAt(new \DateTime('now'));
            $avis->setIsActive(false);
            $avis->setUpdatedAt(new \DateTime('now'));
            $em->persist($avis);
            $em->flush();

            $notification = (new Notification('Avis - Bookfolio', ['email']))
                ->content('Nouvel avis de la part de '.$avis->getUser()->getFullname().' ')
                ->importance(Notification::IMPORTANCE_HIGH);
            $notifier->send($notification, new Recipient('info@book-folio.fr'));

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans("Merci beaucoup! Votre avis compte énormément pour nous. Merci d'avoir pris le temps de nous écrire :)"),
                ]);

            return $this->redirectToRoute('avis_add');
        }

        return $this->render('index/avis/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/release/notes/", name="release_notes")
     */
    public function releaseNotes(ReleaseNotesRepository $releaseNotesRepository, Request $request, PaginatorInterface $paginator)
    {
        $query = $releaseNotesRepository->findActiveByQuery();

        $releases = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->getParameter('PerPage')
        );

        return $this->render('index/release_notes.html.twig', [
            'releases' => $releases,
        ]);
    }

    /**
     * @Route("/avantages", name="avantages")
     */
    public function avantages(AvantagesRepository $avantagesRepository)
    {
        $avantages = $avantagesRepository->findBy(['isActive' => true], ['id' => 'DESC']);

        return $this->render('index/avantages.html.twig', [
            'avantages' => $avantages,
        ]);
    }

    /**
     * @Route("/bug", name="bug")
     */
    public function bug()
    {
        return $this->render('index/index.html.twig', []);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, NotifierInterface $notifier)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $notification = (new Notification('Contact - Bookfolio', ['email']))
                ->content('Nouveau message de la part de '.$form->get('name')->getData().' ')
                ->importance(Notification::IMPORTANCE_HIGH);
            $notifier->send($notification, new Recipient('info@book-folio.fr'));

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans("Message envoyé ! Merci d'avoir pris le temps de nous contacter :)"),
                ]);

            return $this->redirectToRoute('contact');
        }

        return $this->render('index/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
