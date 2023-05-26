<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Upload
{
    public function __construct(
        private readonly string $pathProperty,
        private readonly ?string $smallThumbnailPathProperty = null,
        private readonly ?string $largeThumbnailPathProperty = null,
        private readonly ?string $originalFilenamePathProperty = null,
        private readonly ?string $deleteProperty = null,
        private readonly ?int $maxWidth = null,
        private readonly ?int $maxHeight = null,
    ) {
    }

    public static function fromReflectionAttribute(\ReflectionAttribute $reflectionAttribute): self
    {
        $arguments = $reflectionAttribute->getArguments();

        return new self(
            $arguments['pathProperty'] ?? null,
            $arguments['smallThumbnailPathProperty'] ?? null,
            $arguments['largeThumbnailPathProperty'] ?? null,
            $arguments['originalFilenamePathProperty'] ?? null,
            $arguments['deleteProperty'] ?? null,
            $arguments['maxWidth'] ?? null,
            $arguments['maxHeight'] ?? null
        );
    }

    public function getPathProperty(): ?string
    {
        return $this->pathProperty;
    }

    public function getSmallThumbnailPathProperty(): ?string
    {
        return $this->smallThumbnailPathProperty;
    }

    public function getLargeThumbnailPathProperty(): ?string
    {
        return $this->largeThumbnailPathProperty;
    }

    public function getOriginalFilenamePathProperty(): ?string
    {
        return $this->originalFilenamePathProperty;
    }

    public function getDeleteProperty(): ?string
    {
        return $this->deleteProperty;
    }

    public function getMaxWidth(): ?int
    {
        return $this->maxWidth;
    }

    public function getMaxHeight(): ?int
    {
        return $this->maxHeight;
    }
}
