<?php

namespace App\Controller;

use App\Form\DomainFormType;
use App\Service\AvalableCreditService;
use App\Service\RandomFlashMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @IsGranted("ROLE_USER")
 * @Route("dashboard/manage/book", name="domain_")
 */
class DomainController extends AbstractController
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
     * @Route("/domain", name="index")
     */
    public function Index(Breadcrumbs $breadcrumbs, Request $request)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem('Nom de domaine');

        $form = $this->createForm(DomainFormType::class);

        return $this->render('dashboard/domain/index.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('Configuration de votre book'),
        ]);
    }

    /**
     * Check domaine.
     *
     * @Route("/check", name="check")
     */
    public function Check(Request $request)
    {
        $form = $this->createForm(DomainFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $domain = $form['url']->getData();
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.gandi.net/v5/domain/check?name='.$domain.'',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'authorization: Apikey '.$this->getParameter('gandi_api_key').'',
                ],
            ]);

            $response = curl_exec($curl);
            $response = json_decode($response, true);

            switch ($response['products'][0]['status']) {
                case 'unavailable':
                    return $this->domainUnavailable($request);
                    break;
                case 'available':
                    return $this->domainAvailable($request, $response);
                    break;
            }
        }
    }

    public function domainUnavailable($request)
    {
        $this->get('session')->getFlashBag()
            ->add('notice', [
                'type' => 'error',
                'title' => 'Oups',
                'message' => $this->translator->trans("Ce nom de domaine n'est malheuresement pas disponible."),
            ]);

        return $this->redirect($request->headers->get('referer'));
    }

    public function domainAvailable($request, $response)
    {
        $this->get('session')->getFlashBag()
            ->add('notice', [
                'type' => 'success',
                'title' => 'Cool',
                'message' => $this->translator->trans('Ce nom de domaine est disponible.'),
            ]);

        return $this->redirect($request->headers->get('referer'));
    }
}
