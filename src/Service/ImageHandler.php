<?php

declare(strict_types=1);

namespace App\Service;

use App\Annotation\Upload;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Security;

class ImageHandler
{
    private PropertyAccessor $accessor;

    public function __construct(
        private RandomStringGenerator $randomStringGenerator,
        private ThumbnailGenerator $thumbnailGenerator,
        private Security $security,
        private DiskUsageCalculator $diskUsageCalculator,
        private string $publicPath
    ) {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function upload(object $entity, string $property, Upload $annotation)
    {
        $file = $this->accessor->getValue($entity, $property);

        if ($file instanceof UploadedFile) {
            $user = $this->security->getUser();
            $relativePath = 'uploads/'.$user->getId().'/';
            $absolutePath = $this->publicPath . '/' . $relativePath;

            $generatedName = $this->randomStringGenerator->generate(20);
            $extension = $file->guessExtension();

            $fileName = $generatedName . '_original.' . $extension;
            $this->diskUsageCalculator->hasEnoughSpaceForUpload($user, $file);
            $file->move($absolutePath, $fileName);
            $this->removeOldFile($entity, $annotation);
            $this->accessor->setValue($entity, $annotation->getPath(), $relativePath.$fileName);

            if ($annotation->getSmallThumbnailPath() !== null) {
                $smallThumbnailFileName = $generatedName . '_small.' . $extension;
                $result = $this->thumbnailGenerator->generate($absolutePath.'/'.$fileName, $absolutePath.'/'.$smallThumbnailFileName, 300);
                $this->accessor->setValue($entity, $annotation->getSmallThumbnailPath(), $result ? $relativePath.$smallThumbnailFileName : null);
            }

            if ($annotation->getLargeThumbnailPath() !== null) {
                $largeThumbnailFileName = $generatedName . '_large.' . $extension;
                $result = $this->thumbnailGenerator->generate($absolutePath.'/'.$fileName, $absolutePath.'/'.$largeThumbnailFileName, 600);
                $this->accessor->setValue($entity, $annotation->getLargeThumbnailPath(), $result ? $relativePath.$largeThumbnailFileName : null);
            }

            if ($annotation->getOriginalFilenamePath() !== null) {
                $this->accessor->setValue($entity, $annotation->getOriginalFilenamePath(), $file->getClientOriginalName());
            }
        }
    }

    public function setFileFromFilename(object $entity, string $property, Upload $annotation)
    {
        $path = $this->accessor->getValue($entity, $annotation->getPath());

        if ($path !== null) {
            $file = new File($this->publicPath.'/'.$path,false);
            $this->accessor->setValue($entity, $property, $file);
        }
    }

    public function removeOldFile(object $entity, Upload $annotation)
    {
        if ($annotation->getPath() !== null) {
            $path = $this->accessor->getValue($entity, $annotation->getPath());
            if ($path !== null) {
                @unlink($this->publicPath.'/'.$path);
            }
        }

        if ($annotation->getSmallThumbnailPath() !== null) {
            $path = $this->accessor->getValue($entity, $annotation->getSmallThumbnailPath());
            if ($path !== null) {
                @unlink($this->publicPath.'/'.$path);
            }
        }

        if ($annotation->getLargeThumbnailPath() !== null) {
            $path = $this->accessor->getValue($entity, $annotation->getLargeThumbnailPath());
            if ($path !== null) {
                @unlink($this->publicPath.'/'.$path);
            }
        }
    }
}
