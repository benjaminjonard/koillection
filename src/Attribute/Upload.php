<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Upload
{
    public function __construct(
        private readonly string $path,
        private readonly ?string $extraSmallThumbnailPath = null,
        private readonly ?string $smallThumbnailPath = null,
        private readonly ?string $largeThumbnailPath = null,
        private readonly ?string $originalFilenamePath = null,
        private readonly ?int $extraSmallThumbnailSize = 60,
        private readonly ?int $smallThumbnailSize = 150,
        private readonly ?int $largeThumbnailSize = 300,
        private readonly ?int $maxWidth = null,
        private readonly ?int $maxHeight = null,
    ) {
    }

    public static function fromReflectionAttribute(\ReflectionAttribute $reflectionAttribute): self
    {
        $arguments = $reflectionAttribute->getArguments();

        return new self(
            $arguments['path'] ?? null,
            $arguments['extraSmallThumbnailPath'] ?? null,
            $arguments['smallThumbnailPath'] ?? null,
            $arguments['largeThumbnailPath'] ?? null,
            $arguments['originalFilenamePath'] ?? null,
            $arguments['extraSmallThumbnailSize'] ?? 60,
            $arguments['smallThumbnailSize'] ?? 150,
            $arguments['largeThumbnailSize'] ?? 300,
            $arguments['maxWidth'] ?? null,
            $arguments['maxHeight'] ?? null
        );
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getExtraSmallThumbnailPath(): ?string
    {
        return $this->extraSmallThumbnailPath;
    }

    public function getSmallThumbnailPath(): ?string
    {
        return $this->smallThumbnailPath;
    }

    public function getLargeThumbnailPath(): ?string
    {
        return $this->largeThumbnailPath;
    }

    public function getOriginalFilenamePath(): ?string
    {
        return $this->originalFilenamePath;
    }

    public function getExtraSmallThumbnailSize(): ?int
    {
        return $this->extraSmallThumbnailSize;
    }

    public function getSmallThumbnailSize(): ?int
    {
        return $this->smallThumbnailSize;
    }

    public function getLargeThumbnailSize(): ?int
    {
        return $this->largeThumbnailSize;
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
