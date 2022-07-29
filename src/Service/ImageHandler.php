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
        private readonly RandomStringGenerator $randomStringGenerator,
        private readonly ThumbnailGenerator $thumbnailGenerator,
        private readonly Security $security,
        private readonly DiskUsageCalculator $diskUsageCalculator,
        private readonly string $publicPath
    ) {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function upload(object $entity, string $property, Upload $attribute): void
    {
        $file = $this->accessor->getValue($entity, $property);
        if ($file instanceof UploadedFile) {
            $user = $this->security->getUser();
            $relativePath = 'uploads/'.$user->getId().'/';
            $absolutePath = $this->publicPath.'/'.$relativePath;

            $generatedName = $this->randomStringGenerator->generate(20);
            $extension = $file->guessExtension();

            $fileName = $generatedName.'_original.'.$extension;
            $this->diskUsageCalculator->hasEnoughSpaceForUpload($user, $file);
            $file->move($absolutePath, $fileName);

            $this->removeOldFile($entity, $attribute);
            $this->accessor->setValue($entity, $attribute->getPath(), $relativePath.$fileName);

            if ($attribute->getMaxWidth() || $attribute->getMaxHeight()) {
                $this->thumbnailGenerator->crop($absolutePath.'/'.$fileName, $attribute->getMaxWidth(), $attribute->getMaxHeight());
            }

            if (null !== $attribute->getExtraSmallThumbnailPath()) {
                $extraSmallThumbnailFileName = $generatedName.'_extra_small.'.$extension;
                $result = $this->thumbnailGenerator->generate($absolutePath.'/'.$fileName, $absolutePath.'/'.$extraSmallThumbnailFileName, $attribute->getExtraSmallThumbnailSize());
                $this->accessor->setValue($entity, $attribute->getExtraSmallThumbnailPath(), $result ? $relativePath.$extraSmallThumbnailFileName : null);
            }

            if (null !== $attribute->getSmallThumbnailPath()) {
                $smallThumbnailFileName = $generatedName.'_small.'.$extension;
                $result = $this->thumbnailGenerator->generate($absolutePath.'/'.$fileName, $absolutePath.'/'.$smallThumbnailFileName, $attribute->getSmallThumbnailSize());
                $this->accessor->setValue($entity, $attribute->getSmallThumbnailPath(), $result ? $relativePath.$smallThumbnailFileName : null);
            }

            if (null !== $attribute->getLargeThumbnailPath()) {
                $largeThumbnailFileName = $generatedName.'_large.'.$extension;
                $result = $this->thumbnailGenerator->generate($absolutePath.'/'.$fileName, $absolutePath.'/'.$largeThumbnailFileName, $attribute->getLargeThumbnailSize());
                $this->accessor->setValue($entity, $attribute->getLargeThumbnailPath(), $result ? $relativePath.$largeThumbnailFileName : null);
            }

            if (null !== $attribute->getOriginalFilenamePath()) {
                $this->accessor->setValue($entity, $attribute->getOriginalFilenamePath(), $file->getClientOriginalName());
            }
        }
    }

    public function setFileFromFilename(object $entity, string $property, Upload $attribute): void
    {
        $path = $this->accessor->getValue($entity, $attribute->getPath());

        if (null !== $path) {
            $file = new File($this->publicPath.'/'.$path, false);
            $this->accessor->setValue($entity, $property, $file);
        }
    }

    public function removeOldFile(object $entity, Upload $attribute): void
    {
        if (null !== $attribute->getPath()) {
            $path = $this->accessor->getValue($entity, $attribute->getPath());
            if (null !== $path) {
                @unlink($this->publicPath.'/'.$path);
            }
        }

        if (null !== $attribute->getExtraSmallThumbnailPath()) {
            $path = $this->accessor->getValue($entity, $attribute->getExtraSmallThumbnailPath());
            if (null !== $path) {
                @unlink($this->publicPath.'/'.$path);
            }
        }

        if (null !== $attribute->getSmallThumbnailPath()) {
            $path = $this->accessor->getValue($entity, $attribute->getSmallThumbnailPath());
            if (null !== $path) {
                @unlink($this->publicPath.'/'.$path);
            }
        }

        if (null !== $attribute->getLargeThumbnailPath()) {
            $path = $this->accessor->getValue($entity, $attribute->getLargeThumbnailPath());
            if (null !== $path) {
                @unlink($this->publicPath.'/'.$path);
            }
        }
    }
}
