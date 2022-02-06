<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Upload
{
    public function __construct(
        private string $path,
        private ?string $smallThumbnailPath = null,
        private ?string $largeThumbnailPath = null,
        private ?string $originalFilenamePath = null
    ) {}

    public static function fromReflectionAttribute(\ReflectionAttribute $reflectionAttribute)
    {
        $arguments = $reflectionAttribute->getArguments();

        return new self(
            $arguments['path'] ?? null,
            $arguments['smallThumbnailPath'] ?? null,
            $arguments['largeThumbnailPath'] ?? null,
            $arguments['originalFilenamePath'] ?? null
        );
    }

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