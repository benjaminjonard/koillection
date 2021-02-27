<?php

declare(strict_types=1);

namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Upload
{
    private ?string $path = null;

    private ?string $smallThumbnailPath = null;

    private ?string $largeThumbnailPath = null;

    private ?string $originalFilenamePath = null;


    public function __construct(array $options)
    {
        if (empty($options['path'])) {
            throw new \InvalidArgumentException("Uplodable must have a 'path' property");
        }

        $this->path = $options['path'];
        $this->smallThumbnailPath = $options['smallThumbnailPath'] ?? null;
        $this->largeThumbnailPath = $options['largeThumbnailPath'] ?? null;
        $this->originalFilenamePath = $options['originalFilenamePath'] ?? null;
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