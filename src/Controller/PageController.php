<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageEditFormType;
use App\Form\PageFormType;
use App\Service\AvalableCreditService;
use App\Service\RandomFlashMessage;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @IsGranted("ROLE_USER")
 * @Route("dashboard/pages", name="pages_")
 */
class PageController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, AvalableCreditService $avalableCreditService, RandomFlashMessage $randomFlashMessage)
    {
        $this->translator = $translator;
        $this->avalableCreditService = $avalableCreditService;
        $this->randomFlashMessage = $randomFlashMessage;
    }

    /**
     * Toutes les pages.
     *
     * @Route("/", name="all", methods={"GET","POST"})
     */
    public function Pages(Breadcrumbs $breadcrumbs, Request $request, PaginatorInterface $paginator, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Pages');

        // Récupération des pages de l'utilisateur
        $query = $this->get('doctrine')
            ->getManager()
            ->getRepository('App:Page')
            ->findByUser($this->getUser(), ['createdAt' => 'DESC']);

        $pages = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->getParameter('PerPage')
        );

        $form = $this->createForm(PageFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde dans l'entité
            $page = new Page();
            $page->setUser($this->getUser());
            $page->setTitle($form->get('title')->getData());
            $page->setIsActive(false);
            $page->setSlug($slugger->slug($form->get('title')->getData()));
            $em->persist($page);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', [
                'type' => 'success',
                'title' => $this->randomFlashMessage->getTitle(),
                'message' => $this->translator->trans('La page a bien été créée ! Vous pouvez à présent y ajouter du contenu.'),
            ]);

            return $this->redirectToRoute('pages_show', ['id' => $page->getId()]);
        }

        return $this->render('dashboard/pages/all.html.twig', [
            'pages' => $pages,
            'form' => $form->createView(),
            'title' => $this->translator->trans('Vos pages'),
        ]);
    }

    /**
     * Suppresion d'une page.
     *
     * @Route("/{id}/delete", name="delete")
     */
    public function PageDelete(Request $request, Page $page, EntityManagerInterface $em)
    {
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-page', $submittedToken)) {
            // Récupération de la page
            $query = $em->getRepository('App:Page')->findOneBy(['id' => $page, 'user' => $this->getUser()]);

            // Supprésion
            $em->remove($query);
            $em->flush();

            // Message flash de confirmation de suppression
            $this->get('session')->getFlashBag()->add('notice', [
                'type' => 'success',
                'title' => $this->randomFlashMessage->getTitle(),
                'message' => $this->translator->trans('La page a bien été supprimée'),
            ]);
        }
        // Redirection vers la page de toutes les pages
        return $this->redirectToRoute('pages_all');
    }

    /**
     * Visualisation d'une page.
     *
     * @Route("/{id}/show", name="show")
     */
    public function PageShow(Breadcrumbs $breadcrumbs, Request $request, Page $page, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Pages', $this->get('router')->generate('pages_all'));

        // Récupération des pages de l'utilisateur
        $query = $em->getRepository('App:Page')->findOneBy(['id' => $page, 'user' => $this->getUser()]);
        $pages = $this->get('doctrine')
            ->getManager()
            ->getRepository('App:Page')
            ->findByUser($this->getUser(), ['createdAt' => 'DESC']);

        $breadcrumbs->addItem($query->getTitle());

        $form = $this->createForm(PageEditFormType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde dans l'entité
            $page->setTitle($form->get('title')->getData());
            $page->setContent($form->get('content')->getData());
            $page->setIsActive($form->get('isActive')->getData());
            $page->setUpdatedAt(new \DateTime());
            $page->setSlug($slugger->slug($form->get('title')->getData()));
            $em->persist($page);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', [
                'type' => 'success',
                'title' => $this->randomFlashMessage->getTitle(),
                'message' => $this->translator->trans('Vos modifications ont bien été prises en compte.'),
            ]);

            return $this->redirectToRoute('pages_show', ['id' => $page->getId()]);
        }

        return $this->render('dashboard/pages/show.html.twig', [
            'page' => $query,
            'pages' => $pages,
            'form' => $form->createView(),
            'title' => $page->getTitle(),
        ]);
    }
}
