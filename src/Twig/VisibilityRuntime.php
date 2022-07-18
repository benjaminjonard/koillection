<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\VisibilityEnum;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class VisibilityRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function getVisibilityReason(?string $visibility, string $userVisibility): string
    {
        // Public
        if (null === $visibility && VisibilityEnum::VISIBILITY_PUBLIC === $userVisibility) {
            return $this->translator->trans('global.visibilities.reason.user_public');
        }

        if (VisibilityEnum::VISIBILITY_PUBLIC === $visibility && VisibilityEnum::VISIBILITY_PUBLIC === $userVisibility) {
            return $this->translator->trans('global.visibilities.reason.both_public');
        }

        // Private
        if (VisibilityEnum::VISIBILITY_PRIVATE === $visibility && VisibilityEnum::VISIBILITY_PRIVATE === $userVisibility) {
            return $this->translator->trans('global.visibilities.reason.both_private');
        }

        if (VisibilityEnum::VISIBILITY_PRIVATE === $userVisibility) {
            return $this->translator->trans('global.visibilities.reason.user_private');
        }

        if (VisibilityEnum::VISIBILITY_PRIVATE === $visibility) {
            return $this->translator->trans('global.visibilities.reason.object_private');
        }

        // Internal
        if (VisibilityEnum::VISIBILITY_INTERNAL === $visibility && VisibilityEnum::VISIBILITY_INTERNAL === $userVisibility) {
            return $this->translator->trans('global.visibilities.reason.both_internal');
        }

        if (VisibilityEnum::VISIBILITY_INTERNAL === $userVisibility) {
            return $this->translator->trans('global.visibilities.reason.user_internal');
        }

        if (VisibilityEnum::VISIBILITY_INTERNAL === $visibility) {
            return $this->translator->trans('global.visibilities.reason.object_internal');
        }

        return '';
    }
}
