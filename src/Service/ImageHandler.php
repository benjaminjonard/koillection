<?php

declare(strict_types=1);

namespace App\Service;

use App\Attribute\Upload;
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

    public function upload(object $entity, string $property, Upload $attribute)
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
            $this->removeOldFile($entity, $attribute);
            $this->accessor->setValue($entity, $attribute->getPath(), $relativePath.$fileName);

            if ($attribute->getSmallThumbnailPath() !== null) {
                $smallThumbnailFileName = $generatedName . '_small.' . $extension;
                $result = $this->thumbnailGenerator->generate($absolutePath.'/'.$fileName, $absolutePath.'/'.$smallThumbnailFileName, 300);
                $this->accessor->setValue($entity, $attribute->getSmallThumbnailPath(), $result ? $relativePath.$smallThumbnailFileName : null);
            }

            if ($attribute->getLargeThumbnailPath() !== null) {
                $largeThumbnailFileName = $generatedName . '_large.' . $extension;
                $result = $this->thumbnailGenerator->generate($absolutePath.'/'.$fileName, $absolutePath.'/'.$largeThumbnailFileName, 600);
                $this->accessor->setValue($entity, $attribute->getLargeThumbnailPath(), $result ? $relativePath.$largeThumbnailFileName : null);
            }

            if ($attribute->getOriginalFilenamePath() !== null) {
                $this->accessor->setValue($entity, $attribute->getOriginalFilenamePath(), $file->getClientOriginalName());
            }
        }
    }

    public function setFileFromFilename(object $entity, string $property, Upload $attribute)
    {
        $path = $this->accessor->getValue($entity, $attribute->getPath());

        if ($path !== null) {
            $file = new File($this->publicPath.'/'.$path,false);
            $this->accessor->setValue($entity, $property, $file);
        }
    }

    public function removeOldFile(object $entity, Upload $attribute)
    {
        if ($attribute->getPath() !== null) {
            $path = $this->accessor->getValue($entity, $attribute->getPath());
            if ($path !== null) {
                @unlink($this->publicPath.'/'.$path);
            }
        }

        if ($attribute->getSmallThumbnailPath() !== null) {
            $path = $this->accessor->getValue($entity, $attribute->getSmallThumbnailPath());
            if ($path !== null) {
                @unlink($this->publicPath.'/'.$path);
            }
        }

        if ($attribute->getLargeThumbnailPath() !== null) {
            $path = $this->accessor->getValue($entity, $attribute->getLargeThumbnailPath());
            if ($path !== null) {
                @unlink($this->publicPath.'/'.$path);
            }
        }
    }
}
