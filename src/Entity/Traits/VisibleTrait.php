<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Enum\VisibilityEnum;

trait VisibleTrait
{
    public function updateFinalVisibility(): self
    {
        $this->finalVisibility = match (true) {
            null === $this->parentVisibility => $this->visibility,
            VisibilityEnum::VISIBILITY_PUBLIC === $this->visibility && VisibilityEnum::VISIBILITY_PUBLIC === $this->parentVisibility => VisibilityEnum::VISIBILITY_PUBLIC,
            VisibilityEnum::VISIBILITY_PRIVATE === $this->visibility || VisibilityEnum::VISIBILITY_PRIVATE === $this->parentVisibility => VisibilityEnum::VISIBILITY_PRIVATE,
            default => VisibilityEnum::VISIBILITY_INTERNAL
        };

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getParentVisibility(): ?string
    {
        return $this->parentVisibility;
    }

    public function setParentVisibility(?string $parentVisibility): self
    {
        $this->parentVisibility = $parentVisibility;

        return $this;
    }

    public function getFinalVisibility(): string
    {
        return $this->finalVisibility;
    }

    public function setFinalVisibility(string $finalVisibility): self
    {
        $this->finalVisibility = $finalVisibility;

        return $this;
    }
}
