<?php

namespace App\Controller;

use App\Entity\Guestbook;
use App\Repository\GuestbookRepository;
use App\Repository\UserRepository;
use App\Service\AvalableCreditService;
use App\Service\AwsImageService;
use App\Service\RandomFlashMessage;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\TimeBundle\DateTimeFormatter;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @IsGranted("ROLE_USER")
 * @Route("dashboard/guestbook", name="guestbook_")
 */
class GuestbookController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, AvalableCreditService $avalableCreditService, RandomFlashMessage $randomFlashMessage, AwsImageService $awsImageService)
    {
        $this->translator = $translator;
        $this->avalableCreditService = $avalableCreditService;
        $this->randomFlashMessage = $randomFlashMessage;
        $this->awsImageService = $awsImageService;
    }

    /**
     * Livre d'or en ligne.
     *
     * @Route("/online", name="online")
     */
    public function Guestbook(Breadcrumbs $breadcrumbs, GuestbookRepository $guestbookRepository, PaginatorInterface $paginator, Request $request)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans("Livre d'or"));
        $query = $guestbookRepository->findBy(['user' => $this->getUser(), 'isActive' => true], ['createdAt' => 'DESC']);
        $guestBooks = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->getParameter('PerPage')
        );

        return $this->render('dashboard/guestbook/all.html.twig', ['guestBooks' => $guestBooks]);
    }

    /**
     * Livre d'or hors ligne.
     *
     * @Route("/", name="offline")
     */
    public function GuestbookOffline(Breadcrumbs $breadcrumbs, GuestbookRepository $repos, PaginatorInterface $paginator, Request $request, UserRepository $userRepository, DateTimeFormatter $dateTimeFormatter)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans("Livre d'or"));
        $query = $repos->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);
        $guestBooks = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->getParameter('PerPage')
        );

        return $this->render('dashboard/guestbook/all.html.twig', [
            'guestBooks' => $guestBooks,
            'title' => $this->translator->trans("Votre livre d'or"),
        ]);
    }

    /**
     * Livre d'or : Validation.
     *
     * @Route("/make/{id}/online", name="make_online")
     */
    public function GuestbookMakeOnline(Request $request, Guestbook $guestbook, EntityManagerInterface $em)
    {
        $query = $em->getRepository('App:Guestbook')->findOneBy(['id' => $guestbook, 'user' => $this->getUser()]);
        if ($query) {
            $query->setIsActive(true);
            $em->persist($query);
            $em->flush();
            // Message flash de confirmation
            $this->get('session')->getFlashBag()->add('notice', [
                'type' => 'success',
                'title' => $this->randomFlashMessage->getTitle(),
                'message' => $this->translator->trans('Le message a bien été validé'),
            ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Suppresion d'un message livre d'or.
     *
     * @Route("/{id}/delete", name="delete")
     */
    public function GuestbookDelete(Request $request, Guestbook $guestbook, EntityManagerInterface $em)
    {
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-guestbook', $submittedToken)) {
            // Récupération du message
            $query = $em->getRepository('App:Guestbook')->findOneBy(['id' => $guestbook, 'user' => $this->getUser()]);

            // Supprésion
            $em->remove($query);
            $em->flush();

            // Message flash de confirmation de suppression
            $this->get('session')->getFlashBag()->add('notice', [
                'type' => 'success',
                'title' => $this->randomFlashMessage->getTitle(),
                'message' => $this->translator->trans('Votre message a bien été supprimé'),
            ]);
        }
        // Redirection vers la page de toutes les vidéos
        return $this->redirect($request->headers->get('referer'));
    }
}
