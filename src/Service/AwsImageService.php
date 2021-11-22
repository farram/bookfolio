<?php

namespace App\Service;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class AwsImageService
{
    private $container;
    private $params;

    public function __construct(FilterService $filterService, ContainerBagInterface $params)
    {
        // $this->container = $container;
        $this->filterService = $filterService;
        $this->params = $params;
    }

    public function getPath()
    {
        return $this->filterService->getUrlOfFilteredImage('/', 'avatar');
    }

    public function getPathAvatar($thumb)
    {
        if ($this->s3aws()->doesObjectExist($this->params->get('aws_s3_bucket'), 'avatar' . $thumb)) {
            return $this->filterService->getUrlOfFilteredImage($thumb, 'avatar');
        } else {
            return $this->filterService->getUrlOfFilteredImage('avatar/default-avatar-profile.png', 'avatar');
        }
    }

    public function getPathImage($thumb)
    {
        if ($this->s3aws()->doesObjectExist($this->params->get('aws_s3_bucket'), $thumb)) {
            return $this->filterService->getUrlOfFilteredImage($thumb, 'thumbnail_card');
        } else {
            return null;
        }
    }

    public function getPathImagetest($thumb)
    {
        if ($this->s3aws()->doesObjectExist($this->params->get('aws_s3_bucket'), $thumb)) {
            return $this->filterService->getUrlOfFilteredImage($thumb, 'thumbnail_card');
        } else {
            return null;
        }
    }

    public function getHeadObject($thumb)
    {
        $obj_data = $this->s3aws()->headObject([
            'Bucket' => $this->params->get('aws_s3_bucket'),
            'Key' => $thumb,
        ]);
        //dd($obj_data);
        //"ContentType" => "image/jpeg"
        return $obj_data['ContentLength'];
    }

    public function getPathImageProvider($thumb, $provider)
    {
        if ($this->s3aws()->doesObjectExist($this->params->get('aws_s3_bucket'), $thumb)) {
            return $this->filterService->getUrlOfFilteredImage($thumb, $provider);
        } else {
            return $this->filterService->getUrlOfFilteredImage('layouts/image-not-found.jpg', 'thumbnail_card');
        }
    }

    public function s3aws()
    {


        $credentials = new Credentials($this->params->get('aws_s3_id'), $this->params->get('aws_s3_secret'));
        $s3 = new S3Client([
            'version' => $this->params->get('aws_s3_version'),
            'region' => $this->params->get('aws_s3_region'),
            'credentials' => $credentials,
        ]);

        return $s3;
    }
}
