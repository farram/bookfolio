<?php

namespace App\Controller;

use App\Repository\ReleaseNotesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Urodoz\Truncate\TruncateService;

/**
 *  @Route("/release", name="release_")
 */
class ReleaseController extends AbstractController
{
    /**
     * @Route("/all", name="all")
     */
    public function all()
    {
    }

    /**
     * @Route("short/{page}", name="short")
     */
    public function short(ReleaseNotesRepository $releaseNotesRepository): Response
    {
        $dql = $releaseNotesRepository->findActiveBy();
        $releases = [];
        $truncateService = new TruncateService();

        foreach ($dql as $key => $value) {
            $releases[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
                'content' => $truncateService->truncate($value->getContent(), 100),
                'url' => $this->generateUrl('release_all'),
            ];
        }

        $datas = [
            'releases' => $releases,
            'link' => $this->generateUrl('release_all'),
        ];

        echo json_encode($datas);
        exit;
    }
}
