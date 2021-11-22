<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\GalleryFormType;
use App\Repository\GalleryRepository;
use App\Repository\ImagesRepository;
use App\Service\AvalableCreditService;
use App\Service\AwsImageService;
use App\Service\RandomFlashMessage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Bundle\TimeBundle\DateTimeFormatter;
use Liip\ImagineBundle\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @IsGranted("ROLE_USER")
 * @Route("dashboard/galleries", name="galleries_")
 */
class GalleryController extends AbstractController
{
    public function __construct(
        PasswordHasherFactoryInterface $passwordHasherFactoryInterface,
        TranslatorInterface $translator,
        AvalableCreditService $avalableCreditService,
        RandomFlashMessage $randomFlashMessage,
        AwsImageService $awsImageService
    ) {
        $gallery = new Gallery();
        $this->passwordEncoder = $passwordHasherFactoryInterface->getPasswordHasher($gallery);
        $this->translator = $translator;
        $this->avalableCreditService = $avalableCreditService;
        $this->randomFlashMessage = $randomFlashMessage;
        $this->awsImageService = $awsImageService;
    }

    /**
     * Toutes les galeries.
     *
     * @Route("/", name="index")
     */
    public function index(Breadcrumbs $breadcrumbs, GalleryRepository $galleryRepository)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans('Galeries'));

        return $this->render('dashboard/galleries/all.html.twig', ['galleries' => $this->getListGalleries($galleryRepository), 'title' => $this->translator->trans('Vos galeries')]);
    }

    /**
     * Ajout d'une galerie.
     *
     * @Route("/add", name="add")
     */
    public function add(Breadcrumbs $breadcrumbs, Request $request, EntityManagerInterface $em, GalleryRepository $galleryRepository, SluggerInterface $slugger)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Galeries', $this->get('router')->generate('galleries_index'));
        $breadcrumbs->addItem('Créer une galerie');

        $gallery = new Gallery();
        $form = $this->createForm(GalleryFormType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->avalableCreditService->canAddGalleries()) {
                $i = 2;
                if (!$gallery->getId()) {
                    $gallery->setCreatedAt(new \DateTime('now'));
                }
                $gallery->setUser($this->getUser());
                if ($form->get('passwordHash')->getData()) {
                    $gallery->setIsProtect(true);
                    $plainPassword = $form->get('passwordHash')->getData();
                    $encodePassword = $this->passwordEncoder->encodePassword($plainPassword, null);
                    $gallery->setPasswordHash($encodePassword);
                } else {
                    $gallery->setIsProtect(null);
                }
                $gallery->setPosition(1);
                $gallery->setIsActive($form->get('isActive')->getData());

                $em->persist($gallery);
                $em->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->randomFlashMessage->getTitle(),
                        'message' => $this->translator->trans('Votre nouvelle galerie a bien été créée. Vous pouvez à présent y ajouter des photos.'),
                    ]);

                return $this->redirectToRoute('galleries_show', ['slug' => $gallery->getSlug()]);
            }
        }

        return $this->render('dashboard/galleries/add.html.twig', [
            'galleries' => $this->getListGalleries($galleryRepository),
            'form' => $form->createView(),
            'title' => $this->translator->trans('Nouvelle galerie'),
        ]);
    }

    /**
     * Visualisation de la galerie.
     *
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(Breadcrumbs $breadcrumbs, $slug, GalleryRepository $galleryRepository, ImagesRepository $imagesRepository): Response
    {
        $gallery = $galleryRepository->findOneBy(['slug' => $slug, 'user' => $this->getUser()]);
        if (!$gallery) {
            return $this->redirectToRoute('galleries_index');
        }

        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans('Galeries'), $this->get('router')->generate('galleries_index'));
        $breadcrumbs->addItem($gallery->getName());

        // On récupère la liste des galeries de l'utilisateur
        $user = $this->getUser();
        $images = $imagesRepository->findBy(['gallery' => $gallery, 'user' => $this->getUser()], ['position' => 'ASC']);

        // Récupération de l'image de couverture
        $cover = $gallery->getCoverImage();

        // On vérifie que la cover existe.
        if (null === $cover) {
            // Si ce n'est pas le cas, on récupère la dernière photo publiée.
            //$cover = $this->getLastPublishedImage($gallery->getId());
        }

        return $this->render('dashboard/galleries/show.html.twig', [
            'galleries' => $this->getListGalleries($galleryRepository),
            'gallery' => $gallery,
            'images' => $images,
            'cover' => $cover,
            'title' => $this->translator->trans('Galerie :') . ' ' . $gallery->getName(),
        ]);
    }

    /**
     * Modification d'une galerie.
     *
     * @Route("/{slug}/edit", name="edit", methods={"POST","GET"})
     */
    public function GalleryEdit(GalleryRepository $galleryRepository, $slug, Breadcrumbs $breadcrumbs, Request $request, EntityManagerInterface $em)
    {
        $gallery = $galleryRepository->findOneBy(['slug' => $slug, 'user' => $this->getUser()]);
        if ($gallery) {
            $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
            $breadcrumbs->addItem($this->translator->trans('Galeries'), $this->get('router')->generate('galleries_index'));
            $breadcrumbs->addItem($gallery->getName(), $this->get('router')->generate('galleries_show', ['slug' => $gallery->getSlug()]));
            $breadcrumbs->addItem($this->translator->trans('Modifier'));

            // On récupère la liste des galeries de l'utilisateur
            $galleries = $galleryRepository->findBy(['user' => $this->getUser()], ['id' => 'DESC']);
            $form = $this->createForm(GalleryFormType::class, $gallery);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if (!$gallery->getId()) {
                    $gallery->setCreatedAt(new \DateTime('now'));
                }

                $gallery->setUser($this->getUser());

                if ($form->get('passwordHash')->getData()) {
                    $gallery->setIsProtect(true);
                    $plainPassword = $form->get('passwordHash')->getData();
                    $encodePassword = $this->passwordEncoder->encodePassword($plainPassword, null);
                    $gallery->setPasswordHash($encodePassword);
                }
                $gallery->setIsProtect(false);
                $gallery->setPosition(1);
                $gallery->setIsActive($form->get('isActive')->getData());

                $em->persist($gallery);
                $em->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->randomFlashMessage->getTitle(),
                        'message' => $this->translator->trans('Vos informations ont bien été sauvegardées'),
                    ]);

                return $this->redirect($request->headers->get('referer'));
            }

            $cover = $gallery->getCoverImage();

            return $this->render('dashboard/galleries/edit.html.twig', [
                'galleries' => $galleries,
                'form' => $form->createView(),
                'editMode' => null !== $gallery->getId(),
                'title' => $this->translator->trans('Galerie :') . ' ' . ($gallery->getId() ? $gallery->getName() : null),
                'cover' => $cover,
                'gallery' => $gallery,
                'images' => $gallery->getImages(),
            ]);
        } else {
            return $this->redirectToRoute('galleries_index');
        }
    }

    /**
     * Récupération de la liste des galeries de l'utilisateur.
     */
    public function getListGalleries($galleryRepository)
    {
        return $galleryRepository->findBy(['user' => $this->getUser()], ['position' => 'ASC']);
    }

    /**
     * Galeries.
     *
     * @Route("/galleries/json/{page}", name="json_galleries")
     */
    public function jsonGalleries(ImagesRepository $imagesRepository, GalleryRepository $repository, $page, DateTimeFormatter $dateTimeFormatter, TranslatorInterface $translator, FilterService $filterService)
    {
        $query = $repository->findAllUser($this->getUser());

        $pageSize = 6;
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

        foreach ($paginator as $item) {
            $agoTime = $dateTimeFormatter->formatDiff($item->getCreatedAt(), $now);
            $listImages = $imagesRepository->findImagesFromGallery($item->getId(), $limit = 8);

            $images = [];
            foreach ($listImages as $image) {
                $image_path = $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumbnail_square');
                $images[] = [
                    'id' => $image->getId(),
                    'title' => $image->getTitle(),
                    'thumb' => $image_path,
                    'path' => $callable($image->getImagePath()),
                    'link' => $this->get('router')->generate('media_show', ['gallerySlug' => $item->getSlug(), 'mediaId' => $image->getId()]),
                ];
            }

            $items[] = [
                'gallery' => [
                    'id' => $item->getId(),
                    'slug' => $item->getSlug(),
                    'title' => $item->getName(),
                    'countImages' => count($item->getImages()),
                    'countViews' => count($item->getGalleryViews()),
                    'createdAt' => $translator->trans('Créee') . ' ' . $agoTime,
                    'isProtectText' => (($item->getIsProtect() ? $translator->trans('Privée') : $translator->trans('Publique'))),
                    'isProtect' => $item->getIsProtect(),
                    'description' => $item->getDescription(),
                    'images' => $images,
                    'totalImage' => count($item->getImages()),
                    'link' => $this->get('router')->generate('galleries_show', ['slug' => $item->getSlug()]),
                    'restImages' => count($item->getImages()) - count($listImages),
                    'position' => $item->getPosition(),
                ],
            ];
        }

        $datas = [
            'items' => $items,
            'totalPages' => $pagesCount,
        ];

        echo json_encode($datas);
        exit;
    }

    /**
     * Order galeries.
     *
     * @Route("/galleries/order/{list}", name="order_galleries", methods={"POST"})
     */
    public function GalleriesOrder($list, GalleryRepository $galleryRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $i = 1;

        $result = explode(',', $list);
        foreach ($result as $galleryId) {
            $repo = $galleryRepository->find($galleryId);
            $repo->setPosition($i);
            $em->persist($repo);
            $em->flush();
            ++$i;
        }

        return new Response(null, 204);
    }

    /**
     * Suppression de la galerie.
     *
     * @Route("/gallery/{id}/delete", options={"expose"=true}, name="delete_gallery")
     */
    public function deleteGallery($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $gallery = $entityManager->getRepository(Gallery::class)->findOneBy(['id' => $id, 'user' => $this->getUser()]);
        if ($gallery) {
            $entityManager->remove($gallery);
            $entityManager->flush();
        }

        return new Response();
        exit;
    }

    /**
     * Génération aléatoire des titres de validation.
     */
    public function getTitleFlashRandom()
    {
        $words = ['Énorme !', 'Au top !', 'Yeaaah !', 'Bravo !', 'Super !', 'Hourra !', 'Génial !', 'Cool !', 'Magnifique !', 'Jolie !', 'Fantastique !', 'sensationnel !', 'Parfait !', 'Topissime !', 'Splendide !', 'Fabuleux !', 'Awesome !'];
        $indice = rand(0, count($words) - 1);
        $datas = $words[$indice];

        return $datas;
    }
}
