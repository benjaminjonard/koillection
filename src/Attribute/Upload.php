<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Upload
{
    public function __construct(
        private readonly string $path,
        private readonly ?string $smallThumbnailPath = null,
        private readonly ?string $largeThumbnailPath = null,
        private readonly ?string $originalFilenamePath = null,
        private readonly ?int $maxWidth = null,
        private readonly ?int $maxHeight = null,
    ) {
    }

    public static function fromReflectionAttribute(\ReflectionAttribute $reflectionAttribute): self
    {
        $arguments = $reflectionAttribute->getArguments();

        return new self(
            $arguments['path'] ?? null,
            $arguments['smallThumbnailPath'] ?? null,
            $arguments['largeThumbnailPath'] ?? null,
            $arguments['originalFilenamePath'] ?? null,
            $arguments['maxWidth'] ?? null,
            $arguments['maxHeight'] ?? null
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

    public function getMaxWidth(): ?int
    {
        return $this->maxWidth;
    }

    public function getMaxHeight(): ?int
    {
        return $this->maxHeight;
    }
}
