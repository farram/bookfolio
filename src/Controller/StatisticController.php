<?php

namespace App\Controller;

use App\Repository\StatisticRepository;
use App\Service\AvalableCreditService;
use App\Service\RandomFlashMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @IsGranted("ROLE_USER")
 * @Route("dashboard/statistics", name="statistics_")
 */
class StatisticController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, AvalableCreditService $avalableCreditService, RandomFlashMessage $randomFlashMessage)
    {
        $this->translator = $translator;
        $this->avalableCreditService = $avalableCreditService;
        $this->randomFlashMessage = $randomFlashMessage;
    }

    /**
     * Statistique.
     *
     * @Route("/", name="all")
     */
    public function statistics(Breadcrumbs $breadcrumbs, StatisticRepository $repos)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans('Statistiques'));

        $chartsThisMonth = [];
        foreach ($this->getChartThisMonth($repos) as $data) {
            $date = strftime('%d %B, %Y', strtotime(date_format($data['createdAt'], 'd-m-Y')));
            $chartsThisMonth['count'][] = $data['count'];
            $chartsThisMonth['date'][] = $date;
        }

        $chartsThisYear = [];
        foreach ($this->getChartYear($repos) as $data) {
            $date = strftime('%d %B, %Y', strtotime(date_format($data['createdAt'], 'd-m-Y')));
            $chartsThisYear['count'][] = $data['count'];
            $chartsThisYear['date'][] = $date;
        }

        $chartsLastYear = [];
        foreach ($this->getChartLastYear($repos) as $data) {
            $date = strftime('%d %B, %Y', strtotime(date_format($data['createdAt'], 'd-m-Y')));
            $chartsLastYear['count'][] = $data['count'];
            $chartsLastYear['date'][] = $date;
        }

        return $this->render('dashboard/statistics/all.html.twig', [
            'title' => $this->translator->trans('Vos statistiques de visites'),
            'today' => $this->getChartToday($repos),
            'todayCount' => $this->getCountToday($repos),
            'chartsThisMonthCount' => ($chartsThisMonth ? json_encode($chartsThisMonth['count']) : '0'),
            'chartsThisMonthDate' => ($chartsThisMonth ? json_encode($chartsThisMonth['date']) : '0'),

            'chartsThisYearCount' => ($chartsThisYear ? json_encode($chartsThisYear['count']) : '0'),
            'chartsThisYearDate' => ($chartsThisYear ? json_encode($chartsThisYear['date']) : '0'),

            'chartsLastYearCount' => ($chartsLastYear ? json_encode($chartsLastYear['count']) : '0'),
            'chartsLastYearDate' => ($chartsLastYear ? json_encode($chartsLastYear['date']) : '0'),

            'yesterday' => $this->getChartYesterday($repos),
            'yesterdayCount' => $this->getCountYesterday($repos),

            'thisMonth' => $this->getChartThisMonth($repos),
            'thisMonthCount' => $this->getCountThisMonth($repos),

            'lastMonth' => $this->getChartLastMonth($repos),
            'lastMonthCount' => $this->getCountLastMonth($repos),

            'thisYear' => $this->getChartYear($repos),
            'thisYearCount' => $this->getCountYear($repos),

            'lastYear' => $this->getChartLastYear($repos),
            'lastYearCount' => $this->getCountLastYear($repos),
        ]);
    }

    public function getChartToday($repos)
    {
        return $repos->findTodayByUser($this->getUser());
    }

    public function getCountToday($repos)
    {
        return $repos->findCountTodayByUser($this->getUser());
    }

    public function getChartYesterday($repos)
    {
        return $repos->findYesterdayByUser($this->getUser());
    }

    public function getCountYesterday($repos)
    {
        return $repos->findCountYesterdayByUser($this->getUser());
    }

    public function getChartThisMonth($repos)
    {
        return $repos->findThisMonthByUser($this->getUser());
    }

    public function getCountThisMonth($repos)
    {
        return $repos->findCountThisMonthByUser($this->getUser());
    }

    public function getChartLastMonth($repos)
    {
        return $repos->findLastMonthByUser($this->getUser());
    }

    public function getCountLastMonth($repos)
    {
        return $repos->findCountLastMonthByUser($this->getUser());
    }

    public function getChartYear($repos)
    {
        return $repos->findThisYearByUser($this->getUser());
    }

    public function getCountYear($repos)
    {
        return $repos->findCountThisYearByUser($this->getUser());
    }

    public function getCountLastYear($repos)
    {
        return $repos->findCountLastYearByUser($this->getUser());
    }

    public function getChartLastYear($repos)
    {
        return $repos->findLastYearByUser($this->getUser());
    }
}
