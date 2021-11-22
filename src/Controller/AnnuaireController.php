<?php

namespace App\Controller;

use App\Entity\NotSuggested;
use App\Entity\UnsuggestBook;
use App\Repository\EthnicityRepository;
use App\Repository\ExperienceRepository;
use App\Repository\EyesColorRepository;
use App\Repository\FollowRepository;
use App\Repository\GenderListRepository;
use App\Repository\HairColorRepository;
use App\Repository\ImagesRepository;
use App\Repository\NotSuggestedRepository;
use App\Repository\ProfessionRepository;
use App\Repository\SortListAnnuaireRepository;
use App\Repository\UserRepository;
use App\Service\AwsImageService;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

class AnnuaireController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, FilterService $filterService, SortListAnnuaireRepository $sortListAnnuaireRepository, AwsImageService $awsImageService)
    {
        $this->translator = $translator;
        $this->filterService = $filterService;
        $this->sortListAnnuaireRepository = $sortListAnnuaireRepository;
        $this->awsImageService = $awsImageService;
    }

    /**
     * Nouveaux books.
     *
     * @Route("annuaire/new", name="front_annuaire_new_book")
     */
    public function annuaireNewBook(UserRepository $userRepository, ProfessionRepository $professionRepository, ImagesRepository $imagesRepository)
    {
        $query = $userRepository->findNewUsersForFront(12);
        $truncateService = new TruncateService();
        foreach ($query as $user) {
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

            $filters = $this->get('twig')->getFunctions();
            $callable = $filters['uploaded_asset']->getCallable();
            $listImages = $imagesRepository->findImagesLimit($user, $limit = 3);

            $images = [];
            foreach ($listImages as $image) {
                $image_path = $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumbnail_square');
                $images[] = [
                    'id' => $image->getId(),
                    'title' => $image->getTitle(),
                    'thumb' => $image_path,
                    'path' => $callable($image->getImagePath()),
                    'link' => $this->get('router')->generate('media_show', ['gallerySlug' => $image->getGallery()->getSlug(), 'mediaId' => $image->getId()]),
                ];
            }

            $users[] = [
                'url' => $this->get('router')->generate('portfolio_index', ['name' => $user->getBook()->getName()]),
                'avatar' => $this->awsImageService->getPathAvatar($user->getAvatar()),
                'profession' => $user->getProfession()->getTitle(),
                'location' => $user->getAddress()->getFullAddress(),
                'locationTruncate' => $truncateService->truncate($user->getAddress()->getFullAddress(), 30),
                'fullname' => $truncateService->truncate($user->getFullname(), 30),
                'username' => $user->getUsername(),
                'countGalleries' => $countGalleries,
                'countImages' => $countImages,
                'book' => $user->getBook(),
                'address' => $user->getAddress(),
            ];
        }

        $professions = $professionRepository->findBy(['isActive' => true], ['position' => 'ASC']);
        $list = [];
        foreach ($professions as $key => $value) {
            $list[] = '<div class="w-dyn-items"><div class="w-dyn-item"><a href="'.$this->get('router')->generate('front_annuaire_category', ['slug' => $value->getSlug()]).'" class="product5-category-link">'.$value.'</a></div></div>';
        }

        return $this->render('index/annuaire/new.html.twig', [
            'users' => $users,
            'list' => $list,
        ]);
    }

    /**
     * @Route("annuaire", name="front_annuaire")
     */
    public function annuaireBook(UserRepository $userRepository, ProfessionRepository $professionRepository, FilterService $filterService)
    {
        $users = [];
        $users_datas = $userRepository->findByActive(21);
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
                'fullname' => $user->getFullname(),
                'username' => $user->getUsername(),
                'profession' => $user->getProfession()->getTitle(),
                'countImages' => $countImages,
                'countGalleries' => $countGalleries,
                'address' => $user->getAddress(),
            ];
        }

        // Liste des professions

        $professions = $professionRepository->findBy(['isActive' => true], ['position' => 'ASC']);
        $list = [];
        foreach ($professions as $key => $value) {
            $list[] = '<div class="w-dyn-items"><div class="w-dyn-item"><a href="'.$this->get('router')->generate('front_annuaire_category', ['slug' => $value->getSlug()]).'" class="product5-category-link">'.$value.'</a></div></div>';
        }

        return $this->render('index/annuaire/all.html.twig', [
            'users' => $users,
            'list' => $list,
        ]);
    }

    /**
     * @Route("annuaire/{slug}", name="front_annuaire_category")
     */
    public function annuaireCategory($slug, ProfessionRepository $professionRepository)
    {
        $profession = $professionRepository->findOneBy(['slug' => $slug]);

        $professions = $professionRepository->findBy(['isActive' => true], ['position' => 'ASC']);
        $list = [];
        foreach ($professions as $key => $value) {
            $list[] = '<div class="w-dyn-items"><div class="w-dyn-item"><a href="'.$this->get('router')->generate('front_annuaire_category', ['slug' => $value->getSlug()]).'" class="product5-category-link">'.$value.'</a></div></div>';
        }

        return $this->render('index/annuaire/category.html.twig', [
            'list' => $list,
            'profession' => $profession,
        ]);
    }

    /**
     * @Route("/annuaire/json/profession/{slug}/{page}", name="json_type_artist")
     */
    public function jsonAnnuaireFilterByProfession($slug, ProfessionRepository $professionRepository, Request $request, UserRepository $repository, ProfessionRepository $professionRepos, EthnicityRepository $ethnicityRepos, $page, PaginatorInterface $paginator, FollowRepository $followRepos, HairColorRepository $hairColorRepository, EyesColorRepository $eyesColorRepository, GenderListRepository $genderListRepository, ExperienceRepository $experienceRepository)
    {
        $professionList = $professionRepos->findAll();
        $professions = [];
        foreach ($professionList as $value) {
            $professions[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $getListOrigin = $ethnicityRepos->findAll();

        $originList = [];
        foreach ($getListOrigin as $value) {
            $originList[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $getListHair = $hairColorRepository->findAll();

        $hairList = [];
        foreach ($getListHair as $value) {
            $hairList[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $getListEyes = $eyesColorRepository->findAll();

        $eyesColorList = [];
        foreach ($getListEyes as $value) {
            $eyesColorList[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $getListSexe = $genderListRepository->findAll();

        $list_sexe = [];
        foreach ($getListSexe as $value) {
            $list_sexe[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $profession = $professionRepository->findOneBy(['slug' => $slug]);

        $getListExperience = $experienceRepository->findBy(['profession' => $profession->getId()]);

        $experienceList = [];
        foreach ($getListExperience as $value) {
            $experienceList[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $sortBy = $request->query->get('sortBy');
        $sort = $this->sortListAnnuaireRepository->findOneBy(['title' => $sortBy]);

        $location = $request->query->get('autocomplete');
        $experience = $request->query->get('experience');
        $origin = $request->query->get('origin');
        $hair = $request->query->get('hair');
        $eyesColor = $request->query->get('eyesColor');
        $gender = $request->query->get('sexe');
        $size = $request->query->get('size');
        $weight = $request->query->get('weight');
        $hip = $request->query->get('hip');
        $confection = $request->query->get('confection');
        $pointure = $request->query->get('pointure');

        $query = $repository->findAllWithSearch($profession->getId(), $experience, $location, $origin, $hair, $eyesColor, $gender, $size, $weight, $hip, $confection, $pointure, $sort->getTitle());

        $pageSize = '6';
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $users = [];
        foreach ($paginator as $user) {
            $users[] = [
                'identity' => [
                    'url' => $this->get('router')->generate('portfolio_index', ['name' => $user->getBook()->getName()]),
                    'avatar' => $this->awsImageService->getPathAvatar($user->getAvatar()),
                    'experience' => $user->getProfession()->getTitle(),
                    'location' => $user->getAddress()->getFullAddress(),
                    'fullname' => $user->getFullname(),
                    'username' => $user->getUsername(),
                    'countFolders' => count($user->getGalleries()),
                    'countImages' => count($user->getImages()),
                    'followed' => ($followRepos->findOneBy(['user' => $this->getUser(), 'friend' => $user]) ? true : false),
                    'certified' => $user->getCertified(),
                ],
            ];
        }

        $datas = [
            'users' => $users,
            'totalPages' => $pagesCount,
            'experienceList' => $experienceList,
            'typeList' => $professions,
            'sexeList' => $list_sexe,
            'originList' => $originList,
            'hairList' => $hairList,
            'eyesColorList' => $eyesColorList,
            'sortList' => $this->getListSort(),
            'advancedSearch' => ($profession->getIsAdvancedSearch() ? true : false),
        ];

        echo json_encode($datas);
        exit;
    }

    public function getListSort()
    {
        $sortList = [];
        $sort = $this->sortListAnnuaireRepository->findAllSort();
        foreach ($sort as $value) {
            $sortList[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
                'content' => $value->getDescription(),
            ];
        }

        return $sortList;
    }

    /**
     * @Route("/dashboard/browse/annuaire", name="annuaire_dashboard")
     * @IsGranted("ROLE_USER")
     */
    public function annuaire(TranslatorInterface $translator, Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Annuaire');

        return $this->render('dashboard/annuaire/index.html.twig', ['title' => $this->translator->trans('Annuaire des books')]);
    }

    /**
     * Annuaire JSON.
     *
     * @Route("/dashboard/annuaire/json/{page}", name="json_annuaire")
     * @IsGranted("ROLE_USER")
     */
    public function jsonAnnuaire(Request $request, UserRepository $repository, ProfessionRepository $professionRepos, EthnicityRepository $ethnicityRepos, $page, FilterService $filterService, PaginatorInterface $paginator, FollowRepository $followRepos, HairColorRepository $hairColorRepository, EyesColorRepository $eyesColorRepository, GenderListRepository $genderListRepository, UploaderHelper $uploaderHelper)
    {
        $professionList = $professionRepos->findAll();
        $professions = [];
        foreach ($professionList as $value) {
            $professions[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $getListOrigin = $ethnicityRepos->findAll();

        $originList = [];
        foreach ($getListOrigin as $value) {
            $originList[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $getListHair = $hairColorRepository->findAll();

        $hairList = [];
        foreach ($getListHair as $value) {
            $hairList[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $getListEyes = $eyesColorRepository->findAll();

        $eyesColorList = [];
        foreach ($getListEyes as $value) {
            $eyesColorList[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $getListSexe = $genderListRepository->findAll();

        $list_sexe = [];
        foreach ($getListSexe as $value) {
            $list_sexe[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $profession = $request->query->get('type');
        $experience = $request->query->get('experience');
        $location = $request->query->get('autocomplete');
        $origin = $request->query->get('origin');
        $hair = $request->query->get('hair');
        $eyesColor = $request->query->get('eyesColor');
        $gender = $request->query->get('sexe');
        $size = $request->query->get('size');
        $weight = $request->query->get('weight');
        $hip = $request->query->get('hip');
        $confection = $request->query->get('confection');
        $pointure = $request->query->get('pointure');

        $sortBy = $request->query->get('sortBy');
        $sort = $this->sortListAnnuaireRepository->findOneBy(['title' => $sortBy]);

        $query = $repository->findAllWithSearch($profession, $experience, $location, $origin, $hair, $eyesColor, $gender, $size, $weight, $hip, $confection, $pointure, $sort->getTitle());

        $pageSize = '9';
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $users = [];

        foreach ($paginator as $user) {
            $followers = [];
            $followersData = $followRepos->findBy(['friend' => $user], ['createdAt' => 'DESC']);
            foreach ($followersData as $follower) {
                $followers[] = [
                    'avatar' => $this->awsImageService->getPathAvatar($follower->getUser()->getAvatar()),
                    'fullname' => $follower->getUser()->getFullname(),
                ];
            }

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
                'identity' => [
                    'url' => $this->get('router')->generate('portfolio_index', ['name' => $user->getBook()->getName()]),
                    'avatar' => $this->awsImageService->getPathAvatar($user->getAvatar()),
                    'experience' => $user->getProfession()->getTitle(),
                    'location' => $user->getAddress()->getFullAddress(),
                    'fullname' => $user->getFullname(),
                    'username' => $user->getUsername(),
                    'countFolders' => count($user->getGalleries()),
                    'countImages' => count($user->getImages()),
                    'countVideos' => count($user->getVideos()),
                    'followed' => ($followRepos->findOneBy(['user' => $this->getUser(), 'friend' => $user]) ? true : false),
                    'certified' => $user->getCertified(),
                ],
                'followers' => $followers,
            ];
        }

        $sortList = [];

        $datas = [
            'users' => $users,
            'totalPages' => $pagesCount,
            'typeList' => $professions,
            'sexeList' => $list_sexe,
            'originList' => $originList,
            'hairList' => $hairList,
            'eyesColorList' => $eyesColorList,
            'sortList' => $this->getListSort(),
        ];

        echo json_encode($datas);
        exit;
    }

    /**
     * @Route("/dashboard/annuaire/notsuggested/{uuid}", name="annuaire_notsuggested")
     * @IsGranted("ROLE_USER")
     */
    public function notsuggested($uuid, UserRepository $userRepository, EntityManagerInterface $em, NotSuggestedRepository $notSuggestedRepository): Response
    {
        // On vérifie que l'utilisateur existe
        $book = $userRepository->findOneBy(['uuid' => $uuid]);
        if ($book) {
            // On vérifie qu'il n'a pas déjà été ajouté
            $exist = $notSuggestedRepository->findOneBy(['user' => $this->getUser(), 'book' => $book]);
            if (!$exist) {
                $notSuggested = new NotSuggested();
                $notSuggested->setUser($this->getUser());
                $notSuggested->setBook($book);
                $notSuggested->setCreatedAt(new \DateTime('now'));
                $em->persist($notSuggested);
                $em->flush();

                return new Response(true, 204);
            } else {
                return new Response(false, 403);
            }
        } else {
            return new Response(false, 403);
        }
    }

    /**
     * @Route("/dashboard/browse/annuaire/suggested", name="annuaire_suggested")
     * @IsGranted("ROLE_USER")
     */
    public function annuaireSuggested(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans('Annuaire'), $this->get('router')->generate('annuaire_dashboard'));
        $breadcrumbs->addItem('Suggestions');

        return $this->render('dashboard/annuaire/suggestion.html.twig', ['title' => $this->translator->trans('Suggestion des books à suivre')]);
    }

    /**
     * Annuaire JSON.
     *
     * @Route("/dashboard/annuaire/suggested/json/{page}", name="json_annuaire_suggested")
     * @IsGranted("ROLE_USER")
     */
    public function jsonAnnuaireSuggested(
        Request $request,
        UserRepository $userRepository,
        ProfessionRepository $professionRepos,
        $page,
        PaginatorInterface $paginator,
        FollowRepository $followRepos,
        GenderListRepository $genderListRepository,
        ImagesRepository $imagesRepository
    ) {
        $professionList = $professionRepos->findAll();
        $professions = [];
        foreach ($professionList as $value) {
            $professions[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $profession = $request->query->get('type');
        $experience = $request->query->get('experience');
        $sexe = $request->query->get('sexe');
        $location = $request->query->get('autocomplete');

        $query = $userRepository->findSuggestBooks($this->getUser(), $profession, $experience, $sexe, $location);
        $pageSize = 3;
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $users = [];

        $truncateService = new TruncateService();
        $filters = $this->get('twig')->getFunctions();
        $callable = $filters['uploaded_asset']->getCallable();
        foreach ($paginator as $user) {
            $followers = [];
            $followersData = $followRepos->findBy(['friend' => $user], ['createdAt' => 'DESC']);
            foreach ($followersData as $follower) {
                $followers[] = [
                    'avatar' => $this->awsImageService->getPathAvatar($follower->getUser()->getAvatar()),
                    'fullname' => $follower->getUser()->getFullname(),
                ];
            }

            $images = [];
            $imagesDatas = $imagesRepository->findByUserLimit($user, 5);
            foreach ($imagesDatas as $image) {
                $image_path = $this->awsImageService->getPathImageProvider($image->getImagePath(), 'thumbnail_square');
                $images[] = [
                    'id' => $image->getId(),
                    'title' => $image->getTitle(),
                    'thumb' => $image_path,
                    'path' => $callable($image->getImagePath()),
                ];
            }

            $users[] = [
                'identity' => [
                    'uuid' => $user->getUuid(),
                    'about' => $truncateService->truncate(strip_tags($user->getAbout()), 150),
                    'url' => $this->get('router')->generate('portfolio_index', ['name' => $user->getBook()->getName()]),
                    'avatar' => $this->awsImageService->getPathAvatar($user->getAvatar()),
                    'profession' => $user->getProfession()->getTitle(),
                    'experience' => $user->getExperience()->getTitle(),
                    'location' => $user->getAddress()->getFullAddress(),
                    'locationTruncate' => $truncateService->truncate($user->getAddress()->getFullAddress(), 35),
                    'fullname' => $user->getFullname(),
                    'username' => $user->getUsername(),
                    'followed' => ($followRepos->findOneBy(['user' => $this->getUser(), 'friend' => $user]) ? true : false),
                    'certified' => $user->getCertified(),
                ],
                'followers' => $followers,
                'countFolders' => count($user->getGalleries()),
                'countImages' => count($user->getImages()),
                'countVideos' => count($user->getVideos()),
                'countComments' => count($user->getGuestbooks()),
                'images' => $images,
                'user' => [
                    'uuid' => $this->getUser()->getUuid(),
                ],
            ];
        }

        $genderList = $genderListRepository->findAll();
        $gender_list = [];
        foreach ($genderList as $value) {
            $gender_list[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
            ];
        }

        $datas = [
            'users' => $users,
            'pagesCount' => $pagesCount,
            'typeList' => $professions,
            'sexeList' => $gender_list,
        ];

        echo json_encode($datas);
        exit;
    }

    /**
     * Gestion des books à ne plus suivre.
     *
     * @Route("/neverSuggest/user/{uuid}", name="never_suggest_book")
     */
    public function NeverSuggestBook(UserRepository $userRepository, $uuid, EntityManagerInterface $em): Response
    {
        $user = $userRepository->findOneBy(['uuid' => $uuid]);
        if ($user) {
            $unsuggestBook = new UnsuggestBook();
            $unsuggestBook->setUser($this->security->getUser());
            $unsuggestBook->setBook($user->getUser());
            $unsuggestBook->setCreatedAt(new \DateTime('now'));
            $em->persist($unsuggestBook);
            $em->flush();
        }

        return new Response(null, 204);
    }
}
