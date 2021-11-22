<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\ImageComment;
use App\Entity\ImageLike;
use App\Entity\Images;
use App\Entity\Notification;
use App\Form\ImageCommentFormType;
use App\Form\ImageEditFormType;
use App\Form\ImagesFormType;
use App\Repository\EventsRepository;
use App\Repository\FollowRepository;
use App\Repository\GalleryRepository;
use App\Repository\ImageCommentRepository;
use App\Repository\ImageLikeRepository;
use App\Repository\ImagesRepository;
use App\Repository\ImageViewRepository;
use App\Repository\NotificationRepository;
use App\Service\AvalableCreditService;
use App\Service\AwsImageService;
use App\Service\Mailer;
use App\Service\RandomFlashMessage;
use App\Service\UploaderHelper;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\ColumnChart;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Bundle\TimeBundle\DateTimeFormatter;
use Liip\ImagineBundle\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Urodoz\Truncate\TruncateService;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class ImageController extends AbstractController
{
    public function __construct(
        TranslatorInterface $translator,
        RandomFlashMessage $randomFlashMessage,
        AvalableCreditService $avalableCreditService,
        ImagesRepository $imagesRepository,
        Mailer $mailer,
        FilterService $filterService,
        ImageViewRepository $imageViewRepository,
        AwsImageService $awsImageService
    ) {
        $this->translator = $translator;
        $this->randomFlashMessage = $randomFlashMessage;
        $this->avalableCreditService = $avalableCreditService;
        $this->imagesRepository = $imagesRepository;
        $this->mailer = $mailer;
        $this->filterService = $filterService;
        $this->imageViewRepository = $imageViewRepository;
        $this->awsImageService = $awsImageService;
    }

    /**
     * Visualisation de la photo.
     *
     * @Route("/dashboard/galleries/{gallerySlug}/{mediaId}", name="media_show", methods={"GET","POST"} )
     */
    public function MediaShow(Breadcrumbs $breadcrumbs, $gallerySlug, $mediaId, Request $request, UploaderHelper $uploaderHelper, ImageViewRepository $imageViewRepository, GalleryRepository $galleryRepository, ImagesRepository $imagesRepository)
    {
        $gallery = $galleryRepository->findOneBy(['slug' => $gallerySlug, 'user' => $this->getUser()]);
        if ($gallery) {
            $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
            $breadcrumbs->addItem('Galeries', $this->get('router')->generate('galleries_index'));
            $breadcrumbs->addItem($gallery->getName(), $this->get('router')->generate('galleries_show', ['slug' => $gallery->getSlug()]));

            $entityManager = $this->getDoctrine()->getManager();
            $image = $entityManager->getRepository(Images::class)->findOneBy([
                'id' => $mediaId,
                'gallery' => $gallery->getId(),
                'user' => $this->getUser(),
            ]);
            if (!$image) {
                return $this->redirectToRoute('dashboard_user_galleries');
            }

            $title = 'Image #'.$image->getId();
            $breadcrumbs->addItem($title);

            $formComment = $this->createForm(ImageCommentFormType::class);
            $formComment->handleRequest($request);

            $oldPath = $image->getImagePath();

            $formImage = $this->createForm(ImageEditFormType::class, $image);
            $formImage->handleRequest($request);

            if ($formImage->isSubmitted() && $formImage->isValid()) {
                if ($oldPath != $image->getImagePath()) {
                    $uploaderHelper->renameFile($oldPath, $image->getImagePath());
                }

                $entityManager->persist($image);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->randomFlashMessage->getTitle(),
                        'message' => $this->translator->trans('Vos informations ont bien été sauvegardées'),
                    ]);

                return $this->redirectToRoute('media_show', ['gallerySlug' => $gallery->getSlug(), 'mediaId' => $mediaId]);
            }

            if ($formComment->isSubmitted() && $formComment->isValid()) {
                $imageComment = new ImageComment();
                $imageComment->setImage($image);
                $imageComment->setUser($this->getUser());
                $imageComment->setContent($formComment['content']->getData());
                $imageComment->setIsActive(true);
                $imageComment->setCreatedAt(new \DateTime('now'));
                $entityManager->persist($imageComment);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->randomFlashMessage->getTitle(),
                        'message' => $this->translator->trans('Votre commentaire a bien été pris en compte'),
                    ]);

                return $this->redirectToRoute('media_show', ['gallerySlug' => $gallery->getSlug(), 'mediaId' => $mediaId]);
            }

            $arrayToDataTableThisYear[] = ['Mois', 'Visites', ['role' => 'annotation']];
            foreach ($this->getChartYear($imageViewRepository, $image) as $data) {
                $date = strftime('%d %B, %Y', strtotime(date_format($data['createdAt'], 'd-m-Y')));
                $arrayToDataTableThisYear[] = [$date, floatval($data['count']), floatval($data['count'])];
            }
            $chartThisYear = new ColumnChart();
            $chartThisYear->getData()->setArrayToDataTable($arrayToDataTableThisYear);
            $chartThisYear->getOptions()->getAnnotations()->getTextStyle()->setFontSize(14);
            $chartThisYear->getOptions()->getAnnotations()->getTextStyle()->setAuraColor('none');
            $chartThisYear->getOptions()->getHAxis()->setTitle('Mois');
            $chartThisYear->getOptions()->getVAxis()->setTitle('Nombre de visites');
            $chartThisYear->getOptions()->setHeight(500);

            return $this->render('dashboard/images/show.html.twig', [
                'galleries' => '', //$this->getListGalleries()
                'gallery' => $gallery,
                'image' => $image,
                'title' => $title,
                'formComment' => $formComment->createView(),
                'formImage' => $formImage->createView(),
                'thisYearCount' => $imageViewRepository->findCountThisYearByUser($image),
                'chartThisYear' => $chartThisYear,
            ]);
        }
    }

    /**
     * @Route("/media/likes/{imageId}", name="get_likes_media")
     * @IsGranted("ROLE_USER")
     */
    public function jsonLikesMedia($imageId, ImagesRepository $imagesRepository, FollowRepository $followRepository, ImageLikeRepository $imageLikeRepository, FilterService $filterService)
    {
        $req = $imageLikeRepository->findBy(['image' => $imageId]);
        $likes = [];
        foreach ($req as $like) {
            $filters = $this->get('twig')->getFunctions();
            $callable = $filters['uploaded_asset']->getCallable();
            $listImages = $imagesRepository->findImagesLimit($like->getUser(), $limit = 3);
            $images = [];
            foreach ($listImages as $i) {
                $image_path = $this->awsImageService->getPathImageProvider($i->getImagePath(), 'thumbnail_card');
                $images[] = [
                    'id' => $i->getId(),
                    'title' => $i->getTitle(),
                    'thumb' => $image_path,
                    'path' => $callable($i->getImagePath()),
                    'link' => $this->get('router')->generate('media_show', ['gallerySlug' => $i->getGallery()->getSlug(), 'mediaId' => $i->getId()]),
                ];
            }

            $likes[] = [
                'id' => $like->getId(),
                'identity' => [
                    'url' => $this->get('router')->generate('portfolio_index', ['name' => $like->getUser()->getBook()->getName()]),
                    'avatar' => $this->awsImageService->getPathAvatar($like->getUser()->getAvatar()),
                    'fullname' => $like->getUser()->getFullname(),
                    'username' => $like->getUser()->getUsername(),
                    'followed' => ($followRepository->findOneBy(['user' => $this->getUser(), 'friend' => $like->getUser()]) ? true : false),
                    'profession' => $like->getUser()->getProfession()->getTitle(),
                    'location' => $like->getUser()->getAddress()->getFullAddress(),
                ],
                'images' => $images,
            ];
        }
        $datas = [
            'likes' => $likes,
            'count' => count($images),
        ];
        echo json_encode($datas);
        exit;
    }

    public function getChartYear($repos, $image)
    {
        return $repos->findThisYearByImage($image);
    }

    /**
     * Reorganisation des images dans une galerie.
     *
     * @Route("/gallery/{id}/order/{list}", name="reorder_images", methods={"GET"})
     */
    public function orderImages($list)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $position = 0;
        $result = explode(',', $list);
        foreach ($result as $imageId) {
            $image = $entityManager->getRepository(Images::class)->findOneBy(['id' => $imageId, 'user' => $this->getUser()]);
            $image->setPosition($position++);
            $entityManager->persist($image);
            $entityManager->flush();
        }

        return new Response(null, 204);
    }

    /**
     * Suppression des images an ajax.
     *
     * @Route("/gallery/{id}/media/{mediaId}/remove", name="removeImages", methods={"GET"})
     */
    public function removeImages($id, $mediaId, UploaderHelper $uploaderHelper)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $image = $entityManager->getRepository(Images::class)->findOneBy([
            'id' => $mediaId,
            'user' => $this->getUser(),
        ]);
        $uploaderHelper->deleteFile($image->getImagePath(), false);
        $entityManager->remove($image);
        $entityManager->flush();

        return new Response(null, 204);
    }

    /**
     * Renommage des images dans une galerie.
     *
     * @Route("/gallery/{folderId}/medias/rename/{name}/id/{id}/title/{title}", name="renameImages", methods={"POST"})
     */
    public function renameImages($folderId, $name, $id, $title)
    {
        exit;
        //return new Response(null, 204);
    }

    /**
     * Définition de la photo de couverture.
     *
     * @Route("/gallery/{galleryId}/medias/main/{name}/id/{id}", name="mainImage", methods={"POST"})
     */
    public function mainImage($galleryId, $name, Images $image)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $gallery = $entityManager->getRepository(Gallery::class)->findOneBy(['id' => $galleryId, 'user' => $this->getUser()]);

        $gallery->setCoverImage($image);
        $entityManager->persist($gallery);
        $entityManager->flush();
        exit;
    }

    /**
     * Reorganisation des images dans une galerie.
     *
     * @Route("/gallery/{id}/medias/sort", name="sortImages", methods={"GET"})
     */
    public function sortImages($id)
    {
        $em = $this->getDoctrine()->getManager();
        if (isset($_POST['list'])) {
            $list = json_decode($_POST['list'], true);

            $position = 0;
            foreach ($list as $val) {
                if (!isset($val['id']) || !isset($val['name']) || !isset($val['index'])) {
                    break;
                }

                $image = $em->getRepository(Images::class)->findOneBy(['id' => $val['id'], 'user' => $this->getUser()]);
                $image->setPosition($position++);
                $em->persist($image);
                $em->flush();
            }
        }
        exit;
    }

    /**
     * Reorganisation des images dans une galerie.
     *
     * @Route("/gallery/{id}/medias", name="preload_images", methods={"GET"})
     */
    public function preloadImages($id, FilterService $filterService, ImagesRepository $imagesRepository)
    {
        $preloadedFiles = [];
        $entityManager = $this->getDoctrine()->getManager();
        $gallery = $entityManager->getRepository(Gallery::class)->findOneBy([
            'id' => $id,
            'user' => $this->getUser(),
        ]);
        $images = $imagesRepository->findBy(['user' => $this->getUser(), 'gallery' => $gallery], ['position' => 'ASC']);

        $cover = 0;
        if ($images > 0) {
            foreach ($images as $image) {
                $image_path = $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumbnail_card');

                if ($gallery->getCoverImage()) {
                    if ($image->getId() == $gallery->getCoverImage()->getId()) {
                        $cover = 'border-dashed border-success bg-light-success';
                    } else {
                        $cover = null;
                    }
                }

                $preloadedFiles[] = [
                    'name' => ((null !== $image->getTitle()) ? $image->getTitle() : $image->getImageName()),
                    'type' => ((null !== $image->getType()) ? $image->getType() : 'image/jpeg'),
                    'size' => ((null !== $image->getSize()) ? $image->getSize() : $this->awsImageService->getHeadObject($image->getImagePath())),
                    'size2' => $this->size_as_kb($this->awsImageService->getHeadObject($image->getImagePath())),
                    'file' => $image_path,
                    'data' => [
                        'readerForce' => true,
                        'url' => $this->generateUrl('media_show', ['gallerySlug' => $gallery->getSlug(), 'mediaId' => $image->getId()]),
                        'date' => $image->getCreatedAt(),
                        'isMain' => $cover,
                        'thumbnail' => $image_path,
                        'listProps' => [
                            'id' => $image->getId(),
                        ],
                    ],
                ];
            }
        }
        //dd($preloadedFiles);
        echo json_encode($preloadedFiles);
        exit;
    }

    public function size_as_kb($yoursize)
    {
        if ($yoursize < 1024) {
            return "{$yoursize} bytes";
        } elseif ($yoursize < 1048576) {
            $size_kb = round($yoursize / 1024);

            return "{$size_kb} KB";
        } else {
            $size_mb = round($yoursize / 1048576, 1);

            return "{$size_mb} MB";
        }
    }

    /**
     * Suppression des images an ajax.
     *
     * @Route("/gallery/{id}/medias/add", name="addImages", methods={"POST"})
     */
    public function addImages($id, UploaderHelper $uploaderHelper, Request $request, SluggerInterface $slugger, FilterService $filterService)
    {
        if ($this->avalableCreditService->canPublishImages()) {
            if ($request->isMethod('POST')) {
                $entityManager = $this->getDoctrine()->getManager();
                $gallery = $entityManager->getRepository(Gallery::class)->find($id);

                // Pull the uploaded file information
                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $files = $request->files->get('files');

                if ($files) {
                    $userId = $this->getUser()->getId();
                    $position = 0;
                    $upload = [];
                    foreach ($files as $file) {
                        $defaultTitle = $slugger->slug($file->getClientOriginalName());
                        $newFilename = $uploaderHelper->uploadImages($file, null, $userId, $id, true);

                        // Récupération de la liste des images
                        $images = $entityManager->getRepository(Images::class)->findBy(['gallery' => $id, 'user' => $this->getUser()]);
                        foreach ($images as $image) {
                            $update = $entityManager->getRepository(Images::class)->find($image->getId());
                            $update->setPosition($image->getPosition() + 1);
                            $entityManager->persist($update);
                            $entityManager->flush();
                        }

                        //On sauvegarde dans la BDD
                        $img = new Images();
                        $img->setUser($this->getUser());
                        $img->setImageName($newFilename);
                        $img->setTitle($defaultTitle);
                        $img->setCreatedAt(new \DateTime('now'));
                        $img->setUpdatedAt(new \DateTime('now'));
                        $img->setIsVisible(true);
                        $img->setGallery($gallery);

                        $img->setIsNSFW(false);
                        $img->setIsHome(true);
                        $img->setIsGallery(false);
                        $img->setAllowFavorites(true);
                        $img->setAllowLikes(true);
                        $img->setAllowComments(true);

                        $img->setPosition(99);
                        //$img->setPosition($this->getFirstMediaPositionGallery($id)-1);
                        $img->setType($file->getMimeType());
                        $img->setSize($file->getSize());
                        $img->setCountView(false);
                        $entityManager->persist($img);
                        $entityManager->flush();

                        // Mise à jour de la date de MAJ de la galerie
                        $gallery->setUpdatedAt(new \DateTime('now'));
                        $entityManager->persist($gallery);
                        $entityManager->flush();
                    }
                }
                echo json_encode($upload);
                exit;
            }
        } else {
            return new Response(null, 204);
        }
    }

    /**
     * Définition d'une photo de couverture.
     *
     * @Route("/gallery/media/{id}", name="delete_media", methods={"DELETE"})
     */
    public function DeleteMedia(Request $request, Images $image, UploaderHelper $uploaderHelper, EntityManagerInterface $entityManager)
    {
        $gallery = $image->getGallery();
        $uploaderHelper->deleteFile($image->getImagePath(), false);

        $entityManager->remove($image);
        $entityManager->flush();

        $this->get('session')->getFlashBag()
            ->add('notice', [
                'type' => 'success',
                'title' => $this->randomFlashMessage->getTitle(),
                'message' => $this->translator->trans('Votre photo a bien été supprimée'),
            ]);

        return $this->redirect($request->headers->get('referer'));
    }

    protected function getLastPublishedImage(ImagesRepository $image, $id)
    {
        return $image->findLastFromGallery($id);
        //return $this->getDoctrine()->getRepository(Images::class)->findLastFromGallery($id);
    }

    /**
     * Toutes les images.
     *
     * @Route("/dashboard/images/all", name="image_all")
     * @IsGranted("ROLE_USER")
     */
    public function all(Breadcrumbs $breadcrumbs)
    {
        $images = $this->imagesRepository->findMyAllImage($this->getUser());
        $pageSize = '20';
        $paginator = new Paginator($images);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans('Toutes vos photos'));

        return $this->render('dashboard/images/all.html.twig', [
            'pagesCount' => $pagesCount,
            'title' => $this->translator->trans('Vos photos'),
        ]);
    }

    /**
     * Toutes les images.
     *
     * @Route("/dashboard/images/json/{page}", name="image_all_json")
     * @IsGranted("ROLE_USER")
     */
    public function AllJson($page, DateTimeFormatter $dateTimeFormatter)
    {
        $truncateService = new TruncateService();
        $query = $this->imagesRepository->findMyAllImage($this->getUser());
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

        $items = [];
        $now = new \DateTime();
        foreach ($paginator as $image) {
            $items[] = [
                'id' => $image->getId(),
                'path' => $callable($image->getImagePath()),
                'thumb' => $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumb_large'),
                'title' => $truncateService->truncate($image->getTitle(), 40),
                'date' => $dateTimeFormatter->formatDiff($image->getCreatedAt(), $now),
                'gallery' => $image->getGallery()->getName(),
                'views' => $this->getCountView($image),
                'urlEdit' => $this->get('router')->generate('media_show', ['mediaId' => $image->getId(), 'gallerySlug' => $image->getGallery()->getSlug()]),

                //Route("/dashboard/gallery/{id}/show/image/{mediaId}", name="media_show", methods={"GET","POST"} )
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
     * Suppression d'une image.
     *
     * @Route("/image/{id}/delete", options={"expose"=true}, name="delete_image", methods={"DELETE"})
     */
    public function deleteImage($id, EntityManagerInterface $em, NotificationRepository $notificationRepository, ImageCommentRepository $imageCommentRepository, ImageLikeRepository $imageLikeRepository, ImageViewRepository $imageViewRepository, GalleryRepository $galleryRepository): Response
    {
        $image = $this->imagesRepository->findOneBy(['id' => $id, 'user' => $this->getUser()]);

        if ($image) {
            //On supprime les notifications
            $notifications = $notificationRepository->findAll(['media' => $image]);
            if ($notifications) {
                foreach ($notifications as $notification) {
                    $query = $notificationRepository->find($notification->getId());
                    $em->remove($query);
                }
            }

            //On supprime les likes
            $likes = $imageLikeRepository->findAll(['image' => $image]);
            if ($likes) {
                foreach ($likes as $like) {
                    $query = $imageLikeRepository->find($like->getId());
                    $em->remove($query);
                }
            }

            //On supprime les vues
            $views = $imageViewRepository->findAll(['image' => $image]);
            if ($views) {
                foreach ($views as $view) {
                    $query = $imageViewRepository->find($view->getId());
                    $em->remove($query);
                }
            }

            //On supprime les commentaires
            $comments = $imageCommentRepository->findAll(['image' => $image]);
            if ($comments) {
                foreach ($comments as $comment) {
                    $query = $imageCommentRepository->find($comment->getId());
                    $em->remove($query);
                }
            }

            // On supprime la photo de couverture de la galerie
            $cover = $galleryRepository->findOneBy(['coverImage' => $image]);
            $cover->setCoverImage(null);
            $em->persist($cover);

            $em->remove($image);
            $em->flush();
        }

        return new Response(true, 204);
        exit;
    }

    /**
     * Organisation des images.
     *
     * @Route("/dashboard/images/reorder/", name="images_order", methods={"POST"})
     */
    public function Reorder(Request $request, EntityManagerInterface $em): Response
    {
        $i = 1;
        $lists = json_decode($request->getContent(), true);
        foreach ($lists['list'] as $key => $value) {
            $image = $this->imagesRepository->find($value['id']);
            $image->setPosition($i);
            $em->persist($image);
            $em->flush();
            ++$i;
        }

        return new Response(true, 204);
    }

    public function getCountView($image)
    {
        $views = $this->imageViewRepository->findBy(['image' => $image]);

        return count($views);
    }

    /**
     * Ajout des photos.
     *
     * @Route("/dashboard/images/add", name="image_add")
     * @Route("/medias/add/{id}", name="medias_add_gallery")
     */
    public function temporaryUploadAction(Gallery $gallery = null, Breadcrumbs $breadcrumbs, Request $request, UploaderHelper $uploaderHelper, SluggerInterface $slugger)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Photos', $this->get('router')->generate('image_all'));
        $breadcrumbs->addItem('Ajouter');

        // On récupère la liste des galeries de l'utilisateur
        $entityManager = $this->getDoctrine()->getManager();
        $galleries = $entityManager->getRepository(Gallery::class)->findBy(['user' => $this->getUser()], ['id' => 'DESC']);

        $images = new Images();
        $images->setGallery($gallery);

        $form = $this->createForm(ImagesFormType::class, $images);
        $form->handleRequest($request);

        /** @var Images $image */
        $image = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère l'ID de la galerie posté
            $galleryId = $form->get('gallery')->getData()->getId();
            $gallery = $entityManager->getRepository(Gallery::class)->find($galleryId);

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();

            if ($uploadedFile) {
                $userId = $this->getUser()->getId();
                $position = 0;
                foreach ($uploadedFile as $files) {
                    $defaultTitle = $slugger->slug($files->getClientOriginalName());

                    $newFilename = $uploaderHelper->uploadImages($files, $image->getImageName(), $userId, $galleryId, true);

                    //On sauvegarde dans la BDD
                    $img = new Images();
                    $img->setUser($this->getUser());
                    $img->setImageName($newFilename);
                    $img->setTitle($defaultTitle);
                    $img->setCreatedAt(new \DateTime('now'));
                    $img->setUpdatedAt(new \DateTime('now'));
                    $img->setIsVisible(true);
                    $img->setGallery($gallery);

                    $img->setIsNSFW($form['isNSFW']->getData());
                    $img->setIsHome($form['isHome']->getData());
                    $img->setIsGallery($form['isGallery']->getData());
                    $img->setAllowFavorites($form['allowFavorites']->getData());
                    $img->setAllowLikes($form['allowLikes']->getData());
                    $img->setAllowComments($form['allowComments']->getData());

                    $img->setPosition($this->getFirstMediaPositionGallery($galleryId) - 1);
                    $img->setCountView(false);
                    $entityManager->persist($img);
                    $entityManager->flush();

                    // Mise à jour de la date de MAJ de la galerie
                    $gallery->setUpdatedAt(new \DateTime('now'));
                    $entityManager->persist($gallery);
                    $entityManager->flush();
                }

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => $this->randomFlashMessage->getTitle(),
                        'message' => $this->translator->trans('Votre image a bien été publiée sur votre book'),
                    ]);

                return $this->redirectToRoute('galleries_show', ['slug' => $gallery->getSlug()]);
            }
        }

        return $this->render('dashboard/images/add.html.twig', [
            'galleries' => $galleries,
            'form' => $form->createView(),
            'title' => $this->translator->trans('Ajouter des photos'),
        ]);
    }

    /**
     * @Route("/post/comment/media", name="post_comment_media", methods="POST")
     */
    public function postCommentMedia(EventsRepository $eventsRepository, ImageCommentRepository $imageCommentRepository, ImagesRepository $imagesRepository, Request $request, EntityManagerInterface $em, FilterService $filterService): Response
    {
        $image = $imagesRepository->find($request->request->get('image'));
        if ($image) {
            $comment = new ImageComment();
            $comment->setImage($image);
            $comment->setUser($this->getUser());

            if ($request->request->get('comment_parent')) {
                $commentParent = $imageCommentRepository->find($request->request->get('comment_parent'));
                $comment->setParent($commentParent);
            } else {
                $comment->setParent(null);
            }

            $comment->setContent($request->request->get('comment'));
            $comment->setCreatedAt(new \DateTime('now'));
            $comment->setIsActive(true);
            $em->persist($comment);

            $event = $eventsRepository->findOneBy(['type' => 'comment']);
            $notification = new Notification();
            $notification->setUserWhoFiredEvent($this->getUser());
            $notification->setUserToNotify($image->getUser());
            $notification->setEvent($event);
            $notification->setSeenByUser('no');
            $notification->setMedia($image);
            $notification->setCreatedAt(new \DateTime('now'));
            $notification->setComment($comment);
            $em->persist($notification);

            $em->flush();

            $datas = [
                'image' => $image,
                'book' => $image->getUser(),
                'comment' => $request->request->get('comment'),
                'user' => $this->getUser(),
            ];

            if (true == $image->getUser()->getOption()->getCommentImage()) {
                if ($request->request->get('comment_parent')) {
                    $commentParent = $imageCommentRepository->find($request->request->get('comment_parent'));
                    if ($commentParent->getUser() != $this->getUser()) {
                        $this->mailer->sendEmailAnswerComment($datas);
                    }
                } else {
                    $this->mailer->sendEmailComment($datas);
                }
            }

            return new Response(true, 204);
        } else {
            return new Response(false, 403);
        }
    }

    /**
     * Suppréssion d'un commentaire.
     *
     * @Route("/delete/comment/media", name="delete_comment_media", methods="POST")
     */
    public function deleteCommentMedia(Request $request, ImageCommentRepository $imageCommentRepository, EntityManagerInterface $em)
    {
        $comment = $imageCommentRepository->findOneBy(['id' => $request->request->get('commentId')]);

        if ($comment->getUser() == $this->getUser() || ($comment->getImage()->getUser() == $this->getUser())) {
            $comment->removeImageComment($comment);

            $em->remove($comment);
            $em->flush();

            return new Response(true, 204);
        } else {
            return new Response(false, 403);
        }
    }

    /**
     * Récupération des commentaires de l'image.
     *
     * @Route("/comment/media/{image}/{page}", name="post_comment_media", methods="GET")
     */
    public function commentMedia($image, $page, ImageCommentRepository $imageCommentRepository, FilterService $filterService, DateTimeFormatter $dateTimeFormatter)
    {
        $query = $imageCommentRepository->findActiveBy($image);
        $pageSize = '5';
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $comments = [];
        $truncateService = new TruncateService();
        $now = new \DateTime();

        foreach ($paginator as $comment) {
            // Récupération des réponses
            $getAnswers = $imageCommentRepository->findAnswerBy($image, $comment);
            $answers = [];
            if ($getAnswers) {
                foreach ($getAnswers as $answer) {
                    $answers[] = [
                        'id' => $answer->getId(),
                        'content' => $answer->getContent(),
                        'date' => $agoTime = $dateTimeFormatter->formatDiff($answer->getCreatedAt(), $now),
                        'user' => [
                            'uuid' => $answer->getUser()->getUuid(),
                            'url' => $this->get('router')->generate('portfolio_index', ['name' => $answer->getUser()->getBook()->getName()]),
                            'avatar' => $this->awsImageService->getPathImageProvider($answer->getUser()->getAvatar(), 'avatar'),
                            'fullname' => $answer->getUser()->getFullname(),
                            'profession' => $answer->getUser()->getProfession()->getTitle(),
                        ],
                    ];
                }
            }

            $comments[] = [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'date' => $agoTime = $dateTimeFormatter->formatDiff($comment->getCreatedAt(), $now),
                'answers' => $answers,
                'user' => [
                    'uuid' => $comment->getUser()->getUuid(),
                    'url' => $this->get('router')->generate('portfolio_index', ['name' => $comment->getUser()->getBook()->getName()]),
                    'avatar' => $this->awsImageService->getPathAvatar($comment->getUser()->getAvatar()),
                    'fullname' => $truncateService->truncate($comment->getUser()->getFullname(), 30),
                    'profession' => $comment->getUser()->getProfession()->getTitle(),
                ],
            ];
        }
        $datas = [
            'comments' => $comments,
            'totalPages' => $pagesCount,
            'countTotalComment' => $totalItems,
        ];
        echo json_encode($datas);
        exit;
    }

    /**
     * Récupération de la dernière photo postée par l'utilisateur.
     */
    public function getFirstMediaPositionGallery($galleryId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $images = $entityManager->getRepository(Images::class)->findBy(['user' => $this->getUser(), 'gallery' => $galleryId]);

        // Ensuite on updgrate la position de chaque photo à +1
        foreach ($images as $image) {
            $img = $this->getDoctrine()->getRepository(Images::class)->findOneBy(['id' => $image->getId()]);
            $img->setPosition($img->getPosition() + 1);
            $entityManager->persist($img);
            $entityManager->flush();
        }
        // Et enfin, on récupère le min des photos de l'utilisateur.
        $min = $this->imagesRepository->getMinPosition($galleryId);

        return $min;
    }

    /**
     * Like image.
     *
     * @Route("/like/{action}/image/{image}", name="set_like_media")
     */
    public function LikeMedia(EventsRepository $eventsRepository, ImagesRepository $imagesRepository, ImageLikeRepository $imageLikeRepository, $action, $image, EntityManagerInterface $em)
    {
        switch ($action) {
            case 'like':
                $image = $imagesRepository->find($image);
                $imageLike = new ImageLike();
                $imageLike->setImage($image);
                $imageLike->setUser($this->getUser());
                $em->persist($imageLike);

                if ($this->getUser() != $image->getUser()) {
                    $event = $eventsRepository->findOneBy(['type' => 'like']);
                    $notification = new Notification();
                    $notification->setUserWhoFiredEvent($this->getUser());
                    $notification->setUserToNotify($image->getUser());
                    $notification->setEvent($event);
                    $notification->setSeenByUser('no');
                    $notification->setMedia($image);
                    $notification->setCreatedAt(new \DateTime('now'));
                    $em->persist($notification);
                }
                $em->flush();

                break;
            case 'unlike':
                $like = $imageLikeRepository->findOneBy(['user' => $this->getUser(), 'image' => $image]);
                $em->remove($like);
                $em->flush();
                break;
        }

        return new Response(null, 204);
    }
}
