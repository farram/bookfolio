<?php

namespace App\Service;

use App\Repository\PlanDetailsRepository;
use App\Repository\PlanRepository;

class PlanService
{
    private $planRepository;

    public function __construct(PlanRepository $planRepository, PlanDetailsRepository $planDetailsRepository)
    {
        $this->planRepository = $planRepository;
        $this->planDetailsRepository = $planDetailsRepository;
    }


    public function starter()
    {
        $plan = $this->planRepository->findOneBy(['planPrice' => 0]);
        $planDetails = $this->planDetailsRepository->findBy(['plan' => $plan->getId()]);
        return $planDetails;
    }

    public function all()
    {
        return $this->planRepository->findAll();
    }

    public function byMonth()
    {
        return $this->planRepository->findBy(['type' => 'month'], ['position' => 'ASC']);
    }

    public function byAnnual()
    {
        return $this->planRepository->findBy(['type' => 'annual'], ['position' => 'ASC']);
    }
}
