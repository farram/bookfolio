<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoFormType;
use App\Service\AvalableCreditService;
use App\Service\RandomFlashMessage;
use Doctrine\ORM\EntityManagerInterface;
use Embed\Embed;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @IsGranted("ROLE_USER")
 * @Route("dashboard/videos", name="videos_")
 */
class VideoController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, AvalableCreditService $avalableCreditService, RandomFlashMessage $randomFlashMessage)
    {
        $this->translator = $translator;
        $this->avalableCreditService = $avalableCreditService;
        $this->randomFlashMessage = $randomFlashMessage;
    }

    /**
     * Vidéos.
     *
     * @Route("/", name="all", methods={"GET","POST"})
     */
    public function Videos(Breadcrumbs $breadcrumbs, Request $request, PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Vidéos');

        // Récupération des vidéos de l'utilisateur
        $query = $this->get('doctrine')
            ->getManager()
            ->getRepository('App:Video')
            ->findByUser($this->getUser(), ['createdAt' => 'DESC']);

        $videos = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->getParameter('PerPage')
        );

        $form = $this->createForm(VideoFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Initialisation de l'Embed
            $embed = new Embed();

            // Récupération de des infos de l'url
            $info = $embed->get($form->get('url')->getData());

            // Sauvegarde dans l'entité
            $video = new Video();
            $video->setUser($this->getUser());
            $video->setUrl($info->url);
            $video->setTitle($info->title);
            $video->setPreview($info->image);
            $em->persist($video);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', [
                'type' => 'success',
                'title' => $this->randomFlashMessage->getTitle(),
                'message' => $this->translator->trans('La vidéo a bien été publiée sur votre book.'),
            ]);

            return $this->redirectToRoute('videos_all');
        }

        return $this->render('dashboard/videos/all.html.twig', [
            'videos' => $videos,
            'form' => $form->createView(),
            'title' => $this->translator->trans('Vos vidéos'),
        ]);
    }

    /**
     * Suppresion d'une vidéo.
     *
     * @Route("/{id}/delete", name="delete")
     */
    public function VideoDelete(Request $request, Video $video, EntityManagerInterface $em)
    {
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-video', $submittedToken)) {
            // Récupération de la vidéo
            $query = $em->getRepository('App:Video')->findOneBy(['id' => $video, 'user' => $this->getUser()]);

            // Supprésion
            $em->remove($query);
            $em->flush();

            // Message flash de confirmation de suppression
            $this->get('session')->getFlashBag()->add('notice', [
                'type' => 'success',
                'title' => $this->randomFlashMessage->getTitle(),
                'message' => $this->translator->trans('La vidéo a bien été supprimée.'),
            ]);
        }
        // Redirection vers la page de toutes les vidéos
        return $this->redirectToRoute('videos_all');
    }
}
