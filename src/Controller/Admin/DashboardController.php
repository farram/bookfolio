<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use App\Entity\Page;
use App\Entity\Plan;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\Design;
use App\Entity\Events;
use App\Entity\Images;
use App\Entity\Gallery;
use App\Entity\Annonces;
use App\Entity\Physical;
use App\Entity\Avantages;
use App\Entity\Ethnicity;
use App\Entity\EyesColor;
use App\Entity\HairColor;
use App\Entity\Experience;
use App\Entity\GenderList;
use App\Entity\ImageCover;
use App\Entity\Profession;
use App\Entity\PlanDetails;
use App\Entity\PlanFeature;
use App\Entity\StylePhotos;
use App\Entity\Subscription;
use App\Entity\SortListFollow;
use App\Entity\SortListAnnuaire;
use App\Service\AwsImageService;
use App\Repository\UserRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\FollowRepository;
use App\Repository\ImagesRepository;
use App\Repository\OptionRepository;
use App\Repository\GalleryRepository;
use App\Repository\StatisticRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\StylePhotosRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Bundle\TimeBundle\DateTimeFormatter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\AreaChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\ColumnChart;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        AwsImageService $awsImageService,
        UserRepository $userRepository,
        StatisticRepository $statisticRepository,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->awsImageService = $awsImageService;
        $this->userRepository = $userRepository;
        $this->statisticRepository = $statisticRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Tableau de bord.
     *
     * @Route("/admin", name="admin")
     */
    public function dashboard(ChartBuilderInterface $chartBuilder): Response
    {
        //$users = $this->getDoctrine()->getRepository(User::class)->count([]);
        //$users = $this->userRepository->findBy(['isActive' => true], ['updatedAt' => 'DESC'], 4);
        $users = $this->userRepository->findBy(['isActive' => true]);
        $count_images = $this->getDoctrine()->getRepository(Images::class)->count([]);
        $count_videos = $this->getDoctrine()->getRepository(Video::class)->count([]);
        $count_galleries = $this->getDoctrine()->getRepository(Gallery::class)->count([]);
        $count_pages = $this->getDoctrine()->getRepository(Page::class)->count([]);

        $labels = [];
        $datasets = [];
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $repo = $this->userRepository->findSignupThisYear();
        foreach ($repo as $data) {
            $labels[] = $data['createdAt']->format('d-m-Y');
            $datasets[] = $data['count'];
        }
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Inscriptions',
                    'backgroundColor' => '#4a81d4',
                    'borderColor' => '#4a81d4',
                    'data' => $datasets,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'xAxes' => [
                    'ticks' => [
                        'color' => '#acbfd2',
                        'font' => 6,
                        'padding' => 20,
                    ],
                ],
            ],
            'responsive' => true,
            'title' => [
                'display' => false,
            ],
            'tooltips' => [
                'backgroundColor' => '#4a81d4',
                'bodyFontColor' => '#fff',
                'bodyFontSize' => 14,
            ],
            'legend' => [
                'display' => false,
            ],
        ]);

        $labelsMonth = [];
        $datasetsMonth = [];
        $chartMonth = $chartBuilder->createChart(Chart::TYPE_LINE);
        $repoMonth = $this->userRepository->findSignupThisMonth();
        foreach ($repoMonth as $data) {
            $labelsMonth[] = $data['createdAt']->format('d-m-Y');
            $datasetsMonth[] = $data['count'];
        }
        //dd($labelsMonth);
        $chartMonth->setData([
            'labels' => $labelsMonth,
            'datasets' => [
                [
                    'backgroundColor' => false,
                    'borderColor' => '#4a81d4',
                    'data' => $datasetsMonth,
                ],
            ],
        ]);
        $chartMonth->setOptions([
            'scales' => [
                'xAxes' => [
                    ['ticks' => ['color' => '#fff']],
                ],
            ],
            'responsive' => true,
            'title' => [
                'display' => false,
            ],
            'legend' => [
                'display' => false,
            ],
        ]);

        $labelsTypeBook = [];
        $datasetsTypeBook = [];
        $chartTypeBook = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $userRepository = $this->userRepository->findTypeBook();
        foreach ($userRepository as $data) {
            $labelsTypeBook[] = $data['title'] . ' (' . $data['count'] . ')';
            $datasetsTypeBook[] = $data['count'];
            $colorTypeBook[] = $data['color'];
        }
        $chartTypeBook->setData([
            'labels' => $labelsTypeBook,
            'datasets' => [
                [
                    'label' => 'Inscriptions',
                    'data' => $datasetsTypeBook,
                    'backgroundColor' => $colorTypeBook,
                    'borderWidth' => 0,
                ],
            ],
        ]);

        $chartTypeBook->setOptions([
            'responsive' => true,
            'title' => [
                'display' => true,
                'fontColor' => '#fff',
            ],
            'legend' => [
                'position' => 'right',
                'labels' => [
                    'fontColor' => '#acbfd2',
                ],
            ],
        ]);

        $labelsSubscriber = [];
        $datasetsSubscriber = [];
        $chartSubscriber = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $userRepositorySubscriber = $this->subscriptionRepository->findTypeSubscriber();
        foreach ($userRepositorySubscriber as $data) {
            $labelsSubscriber[] = $data['planName'] . ' (' . $data['count'] . ')';
            $datasetsSubscriber[] = $data['count'];
        }
        $chartSubscriber->setData([
            'labels' => $labelsSubscriber,
            'datasets' => [
                [
                    'label' => 'Inscriptions',
                    'data' => $datasetsSubscriber,
                    'backgroundColor' => ['#00acc1', '#4a81d4'],
                    'borderWidth' => 0,
                    'weight' => 400,
                ],
            ],
        ]);

        $chartSubscriber->setOptions([
            'responsive' => true,
            'title' => [
                'display' => true,
                'fontColor' => '#fff',
            ],
            'legend' => [
                'position' => 'bottom',
                'padding' => 20,
                'labels' => [
                    'fontColor' => '#acbfd2',
                    'padding' => 10,
                ],
            ],
        ]);

        $countSubscribers = $this->subscriptionRepository->findAll();

        return $this->render('Bundles/EasyAdminBundle/dashboard.html.twig', [
            'count_users' => count($users),
            'users' => $users,
            'count_images' => $count_images,
            'count_videos' => $count_videos,
            'count_galleries' => $count_galleries,
            'count_pages' => $count_pages,
            'chart' => $chart,
            'chartTypeBook' => $chartTypeBook,
            'chartMonth' => $chartMonth,
            'chartSubscriber' => $chartSubscriber,
            'countSubscribers' => count($countSubscribers),
        ]);
    }

    /**
     * Galeries.
     *
     * @Route("/admin/galleries/user/{uuid}/{page}", name="json_admin_profile_galleries")
     */
    public function JsonLastGalleries(
        $uuid,
        ImagesRepository $imagesRepository,
        UserRepository $userRepository,
        GalleryRepository $galleryRepository,
        $page,
        DateTimeFormatter $dateTimeFormatter,
        TranslatorInterface $translator
    ): Response {
        $user = $userRepository->findOneBy(['uuid' => $uuid]);
        $query = $galleryRepository->findAllUser($user);

        $pageSize = 5;
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
            $listImages = $imagesRepository->findImagesFromGallery($item->getId(), $limit = null);

            $images = [];
            foreach ($listImages as $image) {
                $agoTimeImage = $dateTimeFormatter->formatDiff($image->getCreatedAt(), $now);

                $image_path = $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumbnail_card');
                $images[] = [
                    'id' => $image->getId(),
                    'title' => $image->getTitle(),
                    'createdAt' => $translator->trans('Publiée') . ' ' . $agoTimeImage,
                    'thumb' => $image_path,
                    // 'thumb_path' => $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumb_large'),
                    'path' => $callable($image->getImagePath()),
                    'link' => $this->get('router')->generate('media_show', ['gallerySlug' => $item->getSlug(), 'mediaId' => $image->getId()]),
                    'stats' => [
                        'view' => count($image->getImageViews()),
                        'comment' => count($image->getImageComments()),
                        'like' => count($image->getImageLikes()),
                    ],
                ];
            }

            $items[] = [
                'gallery' => [
                    'id' => $item->getId(),
                    'slug' => $item->getSlug(),
                    'title' => $item->getName(),
                    'countImages' => count($item->getImages()),
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
     * @Route("/admin/user/{uuid}", name="admin_profile")
     */
    public function profile($uuid, UserRepository $userRepository, FollowRepository $followRepository, StylePhotosRepository $stylePhotosRepository, StatisticRepository $statisticRepository): Response
    {
        $user = $userRepository->findOneBy(['uuid' => $uuid]);

        if ($user) {
            $following = $followRepository->findBy(['friend' => $user->getUser()]);
            $stylePhoto = '';
            if ($user->getBook()->getStylePhotos()) {
                foreach ($user->getBook()->getStylePhotos() as $i => $k) {
                    $name = $stylePhotosRepository->find($k);
                    if ($name) {
                        $stylePhoto .= $name->getTitle() . ', ';
                    }
                }
                $stylePhoto = rtrim($stylePhoto, ', ');
            }

            $arrayToDataTable[] = ['Date', 'Visites'];
            foreach ($this->getChartThisMonth($statisticRepository, $user) as $data) {
                $arrayToDataTable[] = [$data['createdAt'], floatval($data['count'])];
            }

            $chartThisMonth = new AreaChart();
            $chartThisMonth->getData()->setArrayToDataTable($arrayToDataTable);

            $chartThisMonth->getOptions()->setColors(['#2169F5', '#4a81d4']);
            $chartThisMonth->getOptions()->getHAxis()->setTitle('Jours');
            $chartThisMonth->getOptions()->getHAxis()->getTitleTextStyle()->setColor('#333');
            $chartThisMonth->getOptions()->getVAxis()->setMinValue(0);
            $chartThisMonth->getOptions()->getVAxis()->setTitle('Nombre de visites');

            $arrayToDataTableThisYear[] = ['Mois', 'Visites', ['role' => 'annotation']];
            foreach ($this->getChartYear($statisticRepository, $user) as $data) {
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

            return $this->render('Bundles/EasyAdminBundle/profile.html.twig', [
                'user' => $user,
                'following' => $following,
                'stylePhoto' => $stylePhoto,
                'chartThisMonth' => $chartThisMonth,
                'chartThisYear' => $chartThisYear,
                //'chartThisMonth' => $this->getChartThisMonth($statisticRepository, $user),
            ]);
        }
    }

    public function getChartThisMonth($statisticRepository, $user)
    {
        return $statisticRepository->findThisMonthByUser($user);
    }

    public function getChartYear($statisticRepository, $user)
    {
        return $statisticRepository->findThisYearByUser($user);
    }

    /**
     * @Route("/admin/last/images", name="profile")
     */
    public function JsonLastImages(ImagesRepository $imagesRepository, DateTimeFormatter $dateTimeFormatter): Response
    {
        $images = $imagesRepository->findFreshImagesLimit(8);
        $items = [];
        $filters = $this->get('twig')->getFunctions();
        $callable = $filters['uploaded_asset']->getCallable();
        $now = new \DateTime();
        foreach ($images as $image) {
            $thumb = $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumb_medium');
            $items[] = [
                'id' => $image->getId(),
                'path' => $callable($image->getImagePath()),
                'thumb' => $thumb,
                'date' => $dateTimeFormatter->formatDiff($image->getCreatedAt(), $now),
                'user' => [
                    'url' => $this->get('router')->generate('admin_profile', ['uuid' => $image->getUser()->getUuid()]),
                    'fullname' => $image->getUser()->getFullname(),
                    'profession' => $image->getUser()->getProfession()->getTitle(),
                ],
            ];
        }
        $datas = [
            'items' => $items,
        ];

        return new JsonResponse($datas);
        exit;
    }




    /**
     * @Route("/admin/signup/recently", name="json_admin_signup_recently")
     */
    public function JsonSignupRecently(UserRepository $userRepository, DateTimeFormatter $dateTimeFormatter): Response
    {
        $items = [];
        $users = $userRepository->findBy(['isActive' => true], ['createdAt' => 'DESC'], 15);
        $now = new \DateTime();
        foreach ($users as $user) {
            $items[] = [
                'fullname' => $user->getFullname(),
                'id' => $user->getId(),
                'avatar' => $this->awsImageService->getPathAvatar($user->getAvatar()),
                'status' => $user->getProfession()->getTitle(),
                'link' => $this->get('router')->generate('admin_profile', ['uuid' => $user->getUuid()]),
                'created_date' => $dateTimeFormatter->formatDiff($user->getCreatedAt(), $now),
                'count_photos' => count($user->getImages()),
            ];
        }
        $datas = [
            'items' => $items,
        ];

        return new JsonResponse($datas);
        exit;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Bookfolio')
            ->disableUrlSignatures();
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getFullname())
            ->displayUserName(true)
            ->setAvatarUrl($this->awsImageService->getPathAvatar($user->getAvatar()))
            ->displayUserAvatar(true)

            ->addMenuItems([
                MenuItem::linkToRoute('My Profile', 'me-2 fas fa-user', 'home', ['test' => '']),
                MenuItem::linkToRoute('Settings', 'me-2 fas fa-settings', 'home', ['setting' => '']),
            ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'me-2 fas fa-home')->setCssClass('menu-title');

        yield MenuItem::section('Data', '')->setPermission('ROLE_EDITOR')->setCssClass('menu-section text-muted text-uppercase fs-8 ls-1');
        yield MenuItem::linkToCrud('Utilisateurs', 'me-2 fas fa-users', User::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');
        yield MenuItem::linkToCrud('Photograhies', 'me-2 fas fa-image', Images::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');
        yield MenuItem::linkToCrud('Galeries', 'me-2 fas fa-folder', Gallery::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');
        yield MenuItem::linkToCrud('Vidéos', 'me-2 fas fa-video', Video::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');
        yield MenuItem::linkToCrud('Pages', 'me-2 fas fa-file', Page::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');

        // ABONNEMENTS *********************************
        yield MenuItem::section('Abonnements', '')->setPermission('ROLE_EDITOR')->setCssClass('menu-title')->setCssClass('menu-section text-muted text-uppercase fs-8 ls-1');
        yield MenuItem::linkToCrud('Souscriptions', 'me-2 fas fa-credit-card', Subscription::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');
        yield MenuItem::subMenu('Formules', 'me-2 fas fa-credit-card')->setSubItems([
            MenuItem::linkToCrud('Formules abonnements', '', Plan::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title'),
            MenuItem::linkToCrud('Plan détails', '', PlanDetails::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title'),
            MenuItem::linkToCrud('Plan features', '', PlanFeature::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title'),
        ])->setCssClass('menu-title');

        yield MenuItem::section('Données', '')->setPermission('ROLE_EDITOR')->setCssClass('menu-section text-muted text-uppercase fs-8 ls-1');
        yield MenuItem::linkToCrud('Cover', 'me-2 fas fa-image', ImageCover::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');
        yield MenuItem::linkToCrud('Avis', 'me-2 fas fa-message-square', Avis::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');
        yield MenuItem::linkToCrud('Annonces', 'me-2 fas fa-mic', Annonces::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');
        yield MenuItem::linkToCrud('Avantages', 'me-2 fas fa-star', Avantages::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');
        yield MenuItem::linkToCrud('Physical', 'me-2 fas fa-users', Physical::class)->setPermission('ROLE_EDITOR')->setCssClass('menu-title');

        yield MenuItem::section('Liste', '')->setPermission('ROLE_EDITOR')->setCssClass('menu-section text-muted text-uppercase fs-8 ls-1');

        /* Liste */
        yield MenuItem::subMenu('Liste', 'me-2 fas fa-list')->setSubItems([
            MenuItem::linkToCrud('Métiers', '', Profession::class)->setPermission('ROLE_EDITOR'),
            MenuItem::linkToCrud('Expériences', '', Experience::class)->setPermission('ROLE_EDITOR'),
            MenuItem::linkToCrud('Styles photos', '', StylePhotos::class)->setPermission('ROLE_EDITOR'),
            MenuItem::linkToCrud('Designs', '', Design::class)->setPermission('ROLE_EDITOR'),
            MenuItem::linkToCrud('Origin ethnique', '', Ethnicity::class)->setPermission('ROLE_EDITOR'),
            MenuItem::linkToCrud('Couleur des yeux', '', EyesColor::class)->setPermission('ROLE_EDITOR'),
            MenuItem::linkToCrud('Couleur des cheveux', '', HairColor::class)->setPermission('ROLE_EDITOR'),
            MenuItem::linkToCrud('Dropdown annuaire', '', SortListAnnuaire::class)->setPermission('ROLE_EDITOR'),
            MenuItem::linkToCrud('Dropdown follow', '', SortListFollow::class)->setPermission('ROLE_EDITOR'),
            MenuItem::linkToCrud('Gender list', '', GenderList::class)->setPermission('ROLE_EDITOR'),
            MenuItem::linkToCrud('Events', '', Events::class)->setPermission('ROLE_EDITOR'),
        ])->setCssClass('menu-title');
    }
}
