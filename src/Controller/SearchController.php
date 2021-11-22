<?php

namespace App\Controller;

use App\Form\SearchBookFormType;
use App\Repository\FollowRepository;
use App\Repository\ImagesRepository;
use App\Repository\UserRepository;
use App\Service\AwsImageService;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Urodoz\Truncate\TruncateService;

/**
 * @Route("search", name="search_")
 */
class SearchController extends AbstractController
{
    public function __construct(
        AwsImageService $awsImageService
    ) {
        $this->awsImageService = $awsImageService;
    }

    public function searchBar(Request $request): Response
    {
        $form = $this->createForm(SearchBookFormType::class);
        $search = null;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form['username']->getData();

            return $this->redirectToRoute('search_results', ['search' => $search]);
        }
        return $this->render('dashboard/search/bar.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post", name="search_post_bar", methods="POST")
     */
    public function PostSearchBar(UserRepository $userRepository, Request $request)
    {
        $users = [];
        if ($request->get('query')) {
            $query = $userRepository->findUsersQuery($request->get('query'));
            if ($query) {
                $truncateService = new TruncateService();
                foreach ($query as $user) {
                    $users[] = [
                        'identity' => [
                            'url' => $this->get('router')->generate('portfolio_index', ['name' => $user->getBook()->getName()]),
                            'avatar' => $this->awsImageService->getPathAvatar($user->getAvatar()),
                            'profession' => $user->getProfession()->getTitle(),
                            'location' => $user->getAddress()->getFullAddress(),
                            'fullname' => $truncateService->truncate($user->getFullname(), 30),
                        ],
                    ];
                }
            }
        }
        return new JsonResponse($users);
        exit;
    }

    /**
     * @Route("/q/{search}", name="results")
     */
    public function resultsAction($search)
    {
        return $this->render('dashboard/search/result.html.twig', [
            'search' => $search,
        ]);
    }

    /**
     * @Route("/r/{search}/{page}", name="json_result_search")
     */
    public function resultSearchJson($page, $search, ImagesRepository $imagesRepository, UserRepository $userRepository, FilterService $filterService, FollowRepository $followRepository)
    {
        if ($search) {
            $query = $userRepository->findUsers($search);
            $pageSize = '12';
            $paginator = new Paginator($query);
            $totalItems = count($paginator);
            $pagesCount = ceil($totalItems / $pageSize);

            $paginator
                ->getQuery()
                ->setFirstResult($pageSize * ($page - 1))
                ->setMaxResults($pageSize);

            if ($paginator) {
                $truncateService = new TruncateService();

                $users = [];
                foreach ($paginator as $user) {
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
                    $users[] = [
                        'identity' => [
                            'url' => $this->get('router')->generate('portfolio_index', ['name' => $user->getBook()->getName()]),
                            'avatar' => $this->awsImageService->getPathAvatar($user->getAvatar()),
                            'profession' => $user->getProfession()->getTitle(),
                            'location' => $user->getAddress()->getFullAddress(),
                            'locationTruncate' => $truncateService->truncate($user->getAddress()->getFullAddress(), 30),
                            'fullname' => $truncateService->truncate($user->getFullname(), 30),
                            'username' => $user->getUsername(),
                            'countFolders' => $countGalleries,
                            'countMedias' => $countImages,
                            'followed' => ($followRepository->findOneBy(['user' => $this->getUser(), 'friend' => $user]) ? true : false),
                            'certified' => $user->getCertified(),
                        ],
                    ];
                }
            }
            $datas = [
                'users' => $users,
                'totalPages' => $pagesCount,
            ];

            return new JsonResponse($datas);
            exit;
        }
    }
}
