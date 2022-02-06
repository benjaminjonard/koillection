<?php

declare(strict_types=1);

namespace App\Annotation;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Upload
{
    public function __construct(
        private string $path,
        private ?string $smallThumbnailPath = null,
        private ?string $largeThumbnailPath = null,
        private ?string $originalFilenamePath = null
    ) {}

    public function getPath(): ?string
    {
        return $this->path;
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
}