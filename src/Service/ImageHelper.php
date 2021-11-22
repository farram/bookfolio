<?php

namespace App\Service;

use App\Entity\Images;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ImageHelper
{
    private $params;

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function getThumbXs(Images $images)
    {
        $configAws = $this->params->get('aws');
        $s3 = new FileUploader::$S3('files', $configAws);

        return $s3->getFileUrl($images->getThumbXs());
    }

    public function getThumbMd(Images $images)
    {
        $configAws = $this->params->get('aws');
        $s3 = new FileUploader::$S3('files', $configAws);

        return $s3->getFileUrl($images->getThumbMd());
    }

    public function getThumb(Images $images)
    {
        $configAws = $this->params->get('aws');
        $s3 = new FileUploader::$S3('files', $configAws);

        return $s3->getFileUrl($images->getThumb());
    }
}
