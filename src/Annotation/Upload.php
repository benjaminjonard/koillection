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
    /**
     * @var string|null
     */
    private ?string $path = null;

    /**
     * @var string|null
     */
    private ?string $smallThumbnailPath = null;

    /**
     * @var string|null
     */
    private ?string $mediumThumbnailPath = null;


    public function __construct(array $options)
    {
        if (empty($options['path'])) {
            throw new \InvalidArgumentException("Uplodable must have a 'path' property");
        }

        $this->path = $options['path'];
        $this->smallThumbnailPath = $options['smallThumbnailPath'] ?? null;
        $this->mediumThumbnailPath = $options['mediumThumbnailPath'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getSmallThumbnailPath(): ?string
    {
        return $this->smallThumbnailPath;
    }

    /**
     * @return string|null
     */
    public function getMediumThumbnailPath(): ?string
    {
        return $this->mediumThumbnailPath;
    }
}