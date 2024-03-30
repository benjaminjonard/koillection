<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Enum\VisibilityEnum;

trait VisibleTrait
{
    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;
        $this->setFinalVisibility();
        $this->updateDescendantsVisibility();

        return $this;
    }

    public function getParentVisibility(): ?string
    {
        return $this->parentVisibility;
    }

    public function setParentVisibility(?string $parentVisibility): self
    {
        $this->parentVisibility = $parentVisibility;
        $this->setFinalVisibility();
        $this->updateDescendantsVisibility();

        return $this;
    }

    public function getFinalVisibility(): string
    {
        return $this->finalVisibility;
    }

    private function setFinalVisibility(): self
    {
        $this->finalVisibility = match (true) {
            null === $this->parentVisibility => $this->visibility,
            VisibilityEnum::VISIBILITY_PUBLIC === $this->visibility && VisibilityEnum::VISIBILITY_PUBLIC === $this->parentVisibility => VisibilityEnum::VISIBILITY_PUBLIC,
            VisibilityEnum::VISIBILITY_PRIVATE === $this->visibility || VisibilityEnum::VISIBILITY_PRIVATE === $this->parentVisibility => VisibilityEnum::VISIBILITY_PRIVATE,
            default => VisibilityEnum::VISIBILITY_INTERNAL
        };

        return $this;
    }
}
