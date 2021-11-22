<?php

namespace App\Service;

use App\Repository\AnnoncesRepository;
use App\Repository\PlanRepository;
use App\Repository\VideoRepository;
use App\Repository\ImagesRepository;
use App\Repository\GalleryRepository;
use App\Subscription\SubscriptionHelper;
use Symfony\Component\Security\Core\Security;

class AvalableCreditService
{
    private $imagesRepository;
    private $planRepository;
    private $galleryRepository;
    private $videoRepository;
    private $annoncesRepository;

    public function __construct(ImagesRepository $imagesRepository, Security $security, SubscriptionHelper $subscriptionHelper, PlanRepository $planRepository, GalleryRepository $galleryRepository, VideoRepository $videoRepository, AnnoncesRepository $annoncesRepository)
    {
        $this->imagesRepository = $imagesRepository;
        $this->planRepository = $planRepository;
        $this->security = $security;
        $this->subscriptionHelper = $subscriptionHelper;
        $this->galleryRepository = $galleryRepository;
        $this->videoRepository = $videoRepository;
        $this->annoncesRepository = $annoncesRepository;
    }

    public function currentPlan()
    {
        $currentPlan = null;
        if ($this->security->getUser()->hasActiveSubscription()) {
            $currentPlan = $this->subscriptionHelper->findPlan($this->security->getUser()->getSubscription()->getStripePlanId());
        }

        return $currentPlan;
    }

    public function toUsed()
    {
        $date = new \DateTime();
        $month = $date->modify('this month');
        $user = $this->security->getUser();

        return $this->imagesRepository->findCountImagesUploadThisMonth($user, $month);
    }

    public function avalable()
    {
        if ($this->security->getUser()->hasActiveSubscription()) {
            $plan = $this->planRepository->findOneBy(['idPriceApi' => $this->security->getUser()->getSubscription()->getStripePlanId()]);
            if ($plan) {
                return $plan->getPublication();
            }
        } else {
            return $this->subscriptionHelper->getAvalableFree();
        }
    }

    // Ce qui lui reste
    public function remaining()
    {
        return $this->avalable() - $this->toUsed();
    }

    public function progressbar()
    {
        if ($this->remaining() == $this->avalable()) {
            return 100;
        } else {
            return (($this->remaining() * 100) / $this->toUsed()) / 10;
        }
    }

    // On vérifie si l'utilisateur à encore la possibilité de publier une photo
    public function canPublishImages()
    {
        if ($this->toUsed() < $this->avalable()) {
            return true;
        } else {
            return false;
        }
    }



    public function toUsedGallery()
    {
        $date = new \DateTime();
        $month = $date->modify('this month');
        $user = $this->security->getUser();

        return $this->galleryRepository->findCountGalleryAddThisMonth($user, $month);
    }

    public function avalableGalleriesRemaining()
    {
        return $this->avalableGallery() - $this->toUsedGallery();
    }

    public function avalableGallery()
    {
        if ($this->security->getUser()->hasActiveSubscription()) {
            $plan = $this->planRepository->findOneBy(['idPriceApi' => $this->security->getUser()->getSubscription()->getStripePlanId()]);
            if ($plan) {
                return $plan->getPublication();
            }
        } else {
            return $this->subscriptionHelper->getAvalableFreeGallery();
        }
    }

    // On vérifie si l'utilisateur à encore la possibilité de créer une galerie
    public function canAddGalleries()
    {
        if ($this->toUsedGallery() < $this->avalableGallery()) {
            return true;
        } else {
            return false;
        }
    }

    public function canPublishVideos()
    {
        if ($this->toUsedVideos() < $this->avalableVideos()) {
            return true;
        } else {
            return false;
        }
    }

    public function avalableVideosRemaining()
    {
        return $this->avalableVideos() - $this->toUsedVideos();
    }

    public function toUsedVideos()
    {
        $date = new \DateTime();
        $month = $date->modify('this month');
        $user = $this->security->getUser();
        return $this->videoRepository->findCountVideoUploadThisMonth($user, $month);
    }

    public function avalableVideos()
    {
        if ($this->security->getUser()->hasActiveSubscription()) {
            $plan = $this->planRepository->findOneBy(['idPriceApi' => $this->security->getUser()->getSubscription()->getStripePlanId()]);
            if ($plan) {
                return $plan->getPublication();
            }
        } else {
            return $this->subscriptionHelper->getAvalableFreeVideo();
        }
    }

    public function canPublishAnnonces()
    {
        if ($this->toUsedAnnonces() < $this->avalableAnnonces()) {
            return true;
        } else {
            return false;
        }
    }

    public function avalableAnnoncesRemaining()
    {
        return $this->avalableAnnonces() - $this->toUsedAnnonces();
    }

    public function toUsedAnnonces()
    {
        $date = new \DateTime();
        $month = $date->modify('this month');
        $user = $this->security->getUser();
        return $this->annoncesRepository->findCountUploadThisMonth($user, $month);
    }

    public function avalableAnnonces()
    {
        if ($this->security->getUser()->hasActiveSubscription()) {
            $plan = $this->planRepository->findOneBy(['idPriceApi' => $this->security->getUser()->getSubscription()->getStripePlanId()]);
            if ($plan) {
                return $plan->getPublication();
            }
        } else {
            return $this->subscriptionHelper->getAvalableFreeAnnonces();
        }
    }
}
