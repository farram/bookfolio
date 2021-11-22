<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Images;
use App\Service\UploaderHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GalleryImagesController extends DashboardController
{
    /**
     * @Route("dashboard/gallery/{id}/images", name="gallery_add_image", methods={"POST"})
     */
    public function uploadGalleryImage(Gallery $gallery, Request $request, UploaderHelper $uploaderHelper)
    {
        /** @var UploadedFile $uploadedFile */
        $uploadFile = $request->files->get('image');

        $filename = $uploaderHelper->uploadGalleryImage($uploadFile);

        $image = new Images($gallery);
        $image->setImageName($filename);
    }
}
