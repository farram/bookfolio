<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    public const MEDIAS_USER = 'medias';
    public const NOTHUMB = '/assets/img/no-thumb.jpg';
    public const LOCKED = '/assets/img/locked.jpg';
    public const AVATAR_DIR = 'avatar';

    private $filesystem;
    private $requestStackContext;
    private $logger;
    private $publicAssetBaseUrl;

    private const WIDTH = 300;
    private const HEIGHT = 300;

    public function __construct(FilesystemInterface $publicUploadFilesystem, RequestStackContext $requestStackContext, LoggerInterface $logger, string $uploadedAssetsBaseUrl)
    {
        $this->filesystem = $publicUploadFilesystem;
        $this->requestStackContext = $requestStackContext;
        $this->logger = $logger;
        $this->publicAssetBaseUrl = $uploadedAssetsBaseUrl;
        $this->imagine = new Imagine();
    }

    public function uploadImages(File $file, ?string $existingFilename, $userId, $galleryId, bool $isPublic): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFileName();
        }

        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)).'-'.uniqid().'.'.$file->guessExtension();

        $stream = fopen($file->getPathname(), 'r');

        $result = $this->filesystem->writeStream(
            self::MEDIAS_USER.'/'.$userId.'/'.$galleryId.'/'.$newFilename,
            $stream,
            [
                'visibility' => $isPublic ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE,
            ]
        );

        if (false === $result) {
            throw new \Exception(sprintf('Could not write oploaded file "%s%"', $newFilename));
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        if ($existingFilename) {
            try {
                $result = $this->filesystem->delete(self::MEDIAS_USER.'/'.$userId.'/'.$galleryId.'/'.$existingFilename);
                if (false === $result) {
                    throw new \Exception(sprintf('Could not delete old uploaded file "%s%"', $existingFilename));
                }
            } catch (FileNotFoundException $e) {
                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            }
        }

        return $newFilename;
    }

    public function uploadAvatar(File $file, $userId, ?string $existingFilename, bool $isPublic): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFileName();
        }

        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)).'-'.uniqid().'.'.$file->guessExtension();

        $stream = fopen($file->getPathname(), 'r');

        $result = $this->filesystem->writeStream(
            self::AVATAR_DIR.'/'.$userId.'/'.$newFilename,
            $stream,
            [
                'visibility' => $isPublic ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE,
            ]
        );

        if (false === $result) {
            throw new \Exception(sprintf('Could not write oploaded file "%s%"', $newFilename));
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        if ($existingFilename) {
            try {
                $result = $this->filesystem->delete(self::AVATAR_DIR.'/'.$userId.'/'.$existingFilename);
                if (false === $result) {
                    throw new \Exception(sprintf('Could not delete old uploaded file "%s%"', $existingFilename));
                }
            } catch (FileNotFoundException $e) {
                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            }
        }

        return $newFilename;
    }

    public function deleteFile(string $path)
    {
        $result = $this->filesystem->delete($path);
        if (false === $result) {
            throw new \Exception(sprintf('Error deleting "%s"', $path));
        }
    }

    public function renameFile(string $oldPath, string $newPath)
    {
        $response = $this->filesystem->rename($oldPath, $newPath);
        if (false === $response) {
            throw new \Exception(sprintf('Error rename "%s"', $oldPath));
        }
    }

    public function getPublicPath(string $path): string
    {
        $fullPath = $this->publicAssetBaseUrl.'/'.$path;
        // if it's already absolute, just return
        if (false !== strpos($fullPath, '://')) {
            return $fullPath;
        }

        // needed if you deploy under a subdirectory
        return $this->requestStackContext
            ->getBasePath().$fullPath;
    }

    /**
     * @return resource
     */
    public function readStream(string $path)
    {
        $resource = $this->filesystem->readStream($path);

        if (false === $resource) {
            throw new \Exception(sprintf('Error opening stream for "%s"', $path));
        }

        return $resource;
    }

    public function resize($filename)
    {
        list($iwidth, $iheight) = getimagesize($filename);
        $ratio = $iwidth / $iheight;
        $width = self::WIDTH;
        $height = self::HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }
        $photo = $this->imagine->open($filename);
        $photo->resize(new Box($width, $height))->save($filename);

        return $photo;
    }
}
