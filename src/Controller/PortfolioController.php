<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Entity\Gallery;
use App\Entity\GalleryView;
use App\Entity\Guestbook;
use App\Entity\Images;
use App\Entity\ImageView;
use App\Entity\Inbox;
use App\Entity\InboxReply;
use App\Entity\Statistic;
use App\Form\GalleryPasswordFormType;
use App\Form\GuestBookFormType;
use App\Form\PortfolioContactFormType;
use App\Repository\BookRepository;
use App\Repository\FollowRepository;
use App\Repository\GalleryRepository;
use App\Repository\GalleryViewRepository;
use App\Repository\GuestbookRepository;
use App\Repository\ImagesRepository;
use App\Repository\ImageViewRepository;
use App\Repository\InboxStatusRepository;
use App\Repository\PageRepository;
use App\Repository\StatisticRepository;
use App\Repository\StylePhotosRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use App\Service\AwsImageService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Liip\ImagineBundle\Service\FilterService;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/portfolio", name="portfolio_")
 */
class PortfolioController extends AbstractController
{
    private $book;
    private $galleryRepository;
    private $pageRepository;
    private $guestBookRepository;
    private $statisticRepository;
    private $followRepository;
    private $imagesRepository;
    private $userRepository;
    private $passwordEncoder;

    public function __construct(
        Security $security,
        BookRepository $book,
        GalleryRepository $galleryRepository,
        PageRepository $pageRepository,
        GuestbookRepository $guestbookRepository,
        StatisticRepository $statisticRepository,
        RequestStack $requestStack,
        FollowRepository $followRepository,
        ImagesRepository $imagesRepository,
        UserRepository $userRepository,
        PasswordHasherFactoryInterface $passwordHasherFactoryInterface,
        FilterService $filterService,
        AwsImageService $awsImageService
    ) {
        $gallery = new Gallery();
        $this->security = $security;
        $this->book = $book;
        $this->galleryRepository = $galleryRepository;
        $this->pageRepository = $pageRepository;
        $this->guestBookRepository = $guestbookRepository;
        $this->statisticRepository = $statisticRepository;
        $this->requestStack = $requestStack;
        $this->followRepository = $followRepository;
        $this->imagesRepository = $imagesRepository;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordHasherFactoryInterface->getPasswordHasher($gallery);
        $this->filterService = $filterService;
        $this->awsImageService = $awsImageService;
    }

    public function getBook($name)
    {
        $book = $this->book->findOneBy(['name' => $name]);
        $this->setVisitor($book);
        if ($book) {
            return $book;
        }
    }

    public function setVisitor($book)
    {
        if ($book) {
            $already = $this->statisticRepository->findBy(['user' => $book->getUser(), 'createdAt' => new \DateTime(), 'ipAddress' => $this->requestStack->getMasterRequest()->getClientIp()]);
            if (!$already) {
                $em = $this->getDoctrine()->getManager();

                $statistic = new Statistic();
                $statistic->setUser($book->getUser());
                $statistic->setCreatedAt(new \DateTime('now'));
                $statistic->setIpAddress($this->requestStack->getMasterRequest()->getClientIp());
                $em->persist($statistic);
                $em->flush();
            }
        }
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

    public function getPages($book)
    {
        return $this->pageRepository->findBy(['user' => $book->getUser(), 'isActive' => true], ['createdAt' => 'ASC']);
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

    public function IamFollow($book)
    {
        if ($this->security->getUser()) {
            return $this->followRepository->findBy(['user' => $this->security->getUser(), 'friend' => $book->getUser()]);
        } else {
            return false;
        }
    }

    public function getMediasHideHome($book)
    {
        $query = $this->imagesRepository->findPublicImages($book->getUser());
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * portfolio_medias.
     *
     * @Route("/{name}/medias/{page}", name="medias")
     */
    public function Medias($name, $page, ImagesRepository $imagesRepository, FilterService $filterService): Response
    {
        $book = $this->getBook($name);
        $query = $imagesRepository->findAllImagePublic($book);

        $pageSize = 10;
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
        foreach ($paginator as $image) {
            $images[] = [
                'id' => $image->getid(),
                'title' => $image->getTitle(),
                'thumb_path' => $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumb_large'),
                'small' => $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumb_medium'),
                'path' => $callable($image->getImagePath()),
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
     * Vidéos.
     *
     * @Route("/{name}/videos/{page}", name="videos")
     */
    public function Videos($name, $page, VideoRepository $videoRepository): Response
    {
        $book = $this->getBook($name);
        $query = $videoRepository->findByOnline($book->getUser());

        $pageSize = '20';
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $videos = [];
        foreach ($paginator as $video) {
            $videos[] = [
                'id' => $video->getid(),
                'title' => $video->getTitle(),
                'url' => $video->getUrl(),
            ];
        }

        $datas = [
            'videos' => $videos,
            'totalPages' => $pagesCount,
        ];

        echo json_encode($datas);
        exit;
    }

    /**
     * Gallery.
     *
     * @Route("/{name}/gallery/{slug}/{page}", name="medias_from_gallery")
     */
    public function ImagesFromGallery($name, $slug, $page, ImagesRepository $imagesRepository, FilterService $filterService): Response
    {
        $book = $this->getBook($name);
        $gallery = $this->galleryRepository->findOneBy(['slug' => $slug, 'user' => $book->getUser()]);

        $query = $imagesRepository->findAllImageFromGallery($book->getUser(), $gallery);

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
        foreach ($paginator as $image) {
            $images[] = [
                'id' => $image->getid(),
                'title' => $image->getTitle(),
                'thumb_path' => $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumb_large'),
                'path' => $callable($image->getImagePath()),
            ];
        }

        $datas = [
            'images' => $images,
            'totalPages' => $pagesCount,
            'pagination' => $pagesCount,
        ];

        echo json_encode($datas);
        exit;
    }

    /**
     * Page d'accueil.
     *
     * @Route("/{name}", name="index")
     */
    public function BookIndex($name, ImagesRepository $imagesRepository): Response
    {
        $book = $this->getBook($name);

        $showGallery = $imagesRepository->findOneBy(['isVisible' => true, 'user' => $book->getUser(), 'isGallery' => true]);

        return $this->render('portfolio/' . $book->getDesign()->getSlug() . '/index.html.twig', [
            'design' => $book->getDesign()->getSlug(),
            'book' => $book,
            'avatar' => $this->awsImageService->getPathAvatar($book->getUser()->getAvatar()),
            'galleries' => $this->getGalleries($book),
            'pages' => $this->getPages($book),
            'iamFollow' => $this->IamFollow($book),
            'showGallery' => $showGallery,
            'getMediasHideHome' => $this->getMediasHideHome($book),
        ]);
    }

    /**
     * À propos.
     *
     * @Route("/{name}/about", name="about")
     */
    public function BookAbout($name, BookRepository $book, StylePhotosRepository $styleRepos, FollowRepository $follow)
    {
        $book = $this->getBook($name);

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

        return $this->render('portfolio/' . $book->getDesign()->getSlug() . '/about.html.twig', [
            'design' => $book->getDesign()->getSlug(),
            'book' => $book,
            'avatar' => $this->awsImageService->getPathAvatar($book->getUser()->getAvatar()),
            'stylePhoto' => $stylePhoto,
            'galleries' => $this->getGalleries($book),
            'pages' => $this->getPages($book),
            'followBack' => $followBack,
            'iamFollow' => $this->IamFollow($book),
        ]);
    }

    /**
     * Vidéos.
     *
     * @Route("/{name}/videos", name="video")
     */
    public function BookVideos($name, Request $request, PaginatorInterface $paginator, VideoRepository $videoRepository)
    {
        $book = $this->getBook($name);
        if (count($book->getUser()->getVideos()) > 0) {
            $query = $videoRepository->findByOnline($book->getUser());

            $videos = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                $this->getParameter('PerPage')
            );

            return $this->render('portfolio/' . $book->getDesign()->getSlug() . '/videos.html.twig', [
                'design' => $book->getDesign()->getSlug(),
                'book' => $book,
                'avatar' => $this->awsImageService->getPathAvatar($book->getUser()->getAvatar()),
                'galleries' => $this->getGalleries($book),
                'pages' => $this->getPages($book),
                'iamFollow' => $this->IamFollow($book),
                'videos' => $videos,
            ]);
        } else {
            return $this->redirectToRoute('portfolio_index', ['name' => $name]);
        }
    }

    /**
     * Contact.
     *
     * @Route("/{name}/contact", name="contact")
     */
    public function BookContact(Request $request, $name, MailerInterface $mailer, EntityManagerInterface $em, InboxStatusRepository $inboxStatusRepository)
    {
        $book = $this->getBook($name);
        if (true == $book->getShowContact()) {
            $form = $this->createForm(PortfolioContactFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $email = (new TemplatedEmail())
                    ->from('info@book-folio.fr')
                    ->to($book->getUser()->getEmail())
                    ->subject('Nouveau message depuis votre Book !')
                    ->htmlTemplate('emails/portfolio_contact.html.twig')
                    ->context([
                        'user' => $book->getUser(),
                        'name' => $form->get('name')->getData(),
                        'society' => $form->get('society')->getData(),
                        'emailAddress' => $form->get('email')->getData(),
                        'phone' => $form->get('phone')->getData(),
                        'message' => $form->get('message')->getData(),
                    ]);

                $mailer->send($email);

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => 'Cool !',
                        'message' => 'Votre message a bien été envoyé à ' . $book->getUser()->getFullname() . '.',
                    ]);

                // Ajout notifications

                // Ajout dans la BDD

                /*if ($this->security->getUser()) {
                    $inbox->setSender($this->security->getUser());
                } else {
                    $inbox->setSender(null);
                };*/

                //$inbox->setFullname($form->get('name')->getData());
                //$inbox->setCompany($form->get('society')->getData());

                /*if ($this->security->getUser()) {
                    $inbox->setEmail($this->security->getUser()->getEmail());
                } else {
                    $inbox->setEmail($form->get('email')->getData());
                };*/

                //$inbox->setPhone($form->get('phone')->getData());
                //$inbox->setMessage($form->get('message')->getData());

                $uuid = Uuid::uuid4();
                $inbox = new Inbox();
                $inbox->setBook($book->getUser());

                if ($this->security->getUser()) {
                    $inbox->setSender($this->security->getUser());
                } else {
                    $inbox->setSender(null);
                }

                $inbox->setStatus('unread');
                $inbox->setCreatedAt(new \DateTime('now'));
                $inbox->setUuid($uuid);
                $inbox->setIsFavorites(false);
                $inbox->setIsReport(false);
                $em->persist($inbox);

                // Conversation
                $inboxReply = new InboxReply();
                $inboxReply->setInbox($inbox);

                if ($this->security->getUser()) {
                    $inboxReply->setUser($this->security->getUser());
                } else {
                    $inboxReply->setUser(null);
                    $inboxReply->setName($form->get('name')->getData());
                    $inboxReply->setCompagny($form->get('society')->getData());
                    $inboxReply->setEmail($form->get('email')->getData());
                    $inboxReply->setPhone($form->get('phone')->getData());
                }

                $inboxReply->setText($form->get('message')->getData());
                $inboxReply->setCreatedAt(new \DateTime('now'));
                $em->persist($inboxReply);

                $em->flush();

                return $this->redirect($request->headers->get('referer'));
            }

            return $this->render('portfolio/' . $book->getDesign()->getSlug() . '/contact.html.twig', [
                'design' => $book->getDesign()->getSlug(),
                'book' => $book,
                'avatar' => $this->awsImageService->getPathAvatar($book->getUser()->getAvatar()),
                'galleries' => $this->getGalleries($book),
                'pages' => $this->getPages($book),
                'iamFollow' => $this->IamFollow($book),
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('portfolio_index', ['name' => $name]);
        }
    }

    /**
     * setviewmedias.
     *
     * @Route("/media/{image}/view", name="SetviewMedias")
     */
    public function SetviewMedias(Images $image, ImageViewRepository $imageViewRepository): Response
    {
        $already = $imageViewRepository->findBy(['image' => $image, 'ipAddress' => $this->requestStack->getMasterRequest()->getClientIp()]);
        if (!$already) {
            $em = $this->getDoctrine()->getManager();
            $imageView = new ImageView();
            $imageView->setImage($image);
            $imageView->setCreatedAt(new \DateTime('now'));
            $imageView->setIpAddress($this->requestStack->getMasterRequest()->getClientIp());
            $em->persist($imageView);
            $em->flush();
        }

        return new Response();
        exit;
    }

    /**
     * Galeries.
     *
     * @Route("/{name}/galeries", name="galleries")
     */
    public function BookGalleries($name)
    {
        $book = $this->getBook($name);

        return $this->render('portfolio/' . $book->getDesign()->getSlug() . '/galleries.html.twig', [
            'design' => $book->getDesign()->getSlug(),
            'book' => $book,
            'avatar' => $this->awsImageService->getPathAvatar($book->getUser()->getAvatar()),
            'galleries' => $this->getGalleries($book),
            'pages' => $this->getPages($book),
            'iamFollow' => $this->IamFollow($book),
        ]);
    }

    /**
     * Le contenu d'une galerie.
     *
     * @Route("/{name}/galeries/{slug}", name="gallery_show")
     */
    public function BookGalleryShow(ImagesRepository $imagesRepository, $page = 1, PaginatorInterface $paginator, Request $request, $name, $slug, UserPasswordEncoderInterface $passwordEncoder, GalleryViewRepository $galleryRepository)
    {
        $book = $this->getBook($name);

        $gallery = $this->galleryRepository->findOneBy(['user' => $book->getUser(), 'slug' => $slug, 'isActive' => true]);
        if (!$gallery) {
            return $this->redirectToRoute('portfolio_index', ['name' => $name]);
        }

        $images = $imagesRepository->findBy(['user' => $book->getUser(), 'gallery' => $gallery, 'isVisible' => true], ['position' => 'ASC']);

        $already = $galleryRepository->findBy(['gallery' => $gallery, 'createdAt' => new \DateTime(), 'ipAdress' => $this->requestStack->getMasterRequest()->getClientIp()]);
        if (!$already) {
            $em = $this->getDoctrine()->getManager();
            $galleryView = new GalleryView();
            $galleryView->setGallery($gallery);
            $galleryView->setCreatedAt(new \DateTime('now'));
            $galleryView->setIpAdress($this->requestStack->getMasterRequest()->getClientIp());
            $em->persist($galleryView);
            $em->flush();
        }

        // Si la galerie est protégée par un mot de passe
        // On affiche le formulaire
        $form = $this->createForm(GalleryPasswordFormType::class);
        $form->handleRequest($request);
        if (true == $gallery->getIsProtect()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $plainPassword = $form->get('passwordHash')->getData();
                $validPassword = password_verify(
                    $plainPassword,
                    $gallery->getPasswordHash(),
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

        return $this->render('portfolio/' . $book->getDesign()->getSlug() . '/gallery_show.html.twig', [
            'design' => $book->getDesign()->getSlug(), // Path du design
            'book' => $book, // les infos du book concerné
            'avatar' => $this->awsImageService->getPathAvatar($book->getUser()->getAvatar()),
            'galleries' => $this->getGalleries($book), // La liste des gallerie. Affichée en sous menu du le header du portfolio
            'gallery' => $gallery, // La galerie courante
            'form' => $form->createView(), // Formulaire de mot de passe
            'pages' => $this->getPages($book), // Liste des pages. Affichée dans le menu du header du portfolio
            'iamFollow' => $this->IamFollow($book), // Afficher dans le footer. Possibilité de follow ou unfollow
            'images' => $images, // Liste des images de la galerie
        ]);
    }

    /**
     * Commentaires.
     *
     * @Route("/{name}/commentaires", name="guestbook")
     */
    public function BookGuestbook(Request $request, $name, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $book = $this->getBook($name);
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

            return $this->render('portfolio/' . $book->getDesign()->getSlug() . '/comments.html.twig', [
                'design' => $book->getDesign()->getSlug(),
                'book' => $book,
                'avatar' => $this->awsImageService->getPathAvatar($book->getUser()->getAvatar()),
                'galleries' => $this->getGalleries($book),
                'pages' => $this->getPages($book),
                'comments' => $comments,
                'form' => $form->createView(),
                'iamFollow' => $this->IamFollow($book),
            ]);
        } else {
            return $this->redirectToRoute('portfolio_index', ['name' => $name]);
        }
    }

    /**
     * Follow.
     *
     * @Route("/{name}/follow/add", name="add_follow")
     * @IsGranted("ROLE_USER")
     */
    public function AddFollow(Request $request, $name)
    {
        $book = $this->getBook($name);

        if (false == $this->IamFollow($book)) {
            $em = $this->getDoctrine()->getManager();

            $follow = new Follow();
            $follow->setUser($this->getUser());
            $follow->setFriend($book->getUser());
            $follow->setNotificationsInterface(false);
            $follow->setCreatedAt(new \DateTime('now'));
            $em->persist($follow);
            $em->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => 'C\'est fait !',
                    'message' => 'Vous commencez à suivre ' . $book->getUser()->getFirstname() . '.',
                ]);

            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * Follow.
     *
     * @Route("/{name}/follow/add", name="add_follow")
     * @IsGranted("ROLE_USER")
     */
    public function AddFollowFromBook(Request $request, $name)
    {
        $book = $this->getBook($name);

        if (false == $this->IamFollow($book)) {
            $em = $this->getDoctrine()->getManager();

            $follow = new Follow();
            $follow->setUser($this->getUser());
            $follow->setFriend($book->getUser());
            $follow->setNotificationsInterface(false);
            $follow->setCreatedAt(new \DateTime('now'));
            $em->persist($follow);
            $em->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => 'C\'est fait !',
                    'message' => 'Vous commencez à suivre ' . $book->getUser()->getFirstname() . '.',
                ]);

            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * Follow.
     *
     * @Route("/{name}/follow/remove", name="remove_follow")
     */
    public function RemoveFollow(Request $request, $name)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $book = $this->getBook($name);
        if (true == $this->IamFollow($book)) {
            $em = $this->getDoctrine()->getManager();

            $follow = $this->followRepository->findOneBy(['user' => $this->security->getUser(), 'friend' => $book->getUser()]);
            $em->remove($follow);
            $em->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => 'Dommage ...',
                    'message' => 'Vous ne suivez plus ' . $book->getUser()->getFirstname() . '.',
                ]);

            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * Page.
     *
     * @Route("/{name}/{slug}", name="page")
     */
    public function BookPage($name, $slug, PageRepository $page)
    {
        $book = $this->getBook($name);
        $page = $page->findOneBy(['slug' => $slug, 'user' => $book->getUser(), 'isActive' => true]);

        return $this->render('portfolio/' . $book->getDesign()->getSlug() . '/page.html.twig', [
            'design' => $book->getDesign()->getSlug(),
            'book' => $book,
            'avatar' => $this->awsImageService->getPathAvatar($book->getUser()->getAvatar()),
            'galleries' => $this->getGalleries($book),
            'pages' => $this->getPages($book),
            'page' => $page,
            'iamFollow' => $this->IamFollow($book),
        ]);
    }
}
