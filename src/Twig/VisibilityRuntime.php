<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Tag;
use App\Enum\VisibilityEnum;
use App\Model\BreadcrumbElement;
use App\Repository\TagRepository;
use App\Service\ContextHandler;
use App\Service\FeatureChecker;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class VisibilityRuntime implements RuntimeExtensionInterface
{
    public function __construct(private TranslatorInterface $translator) {}

    public function getVisibilityReason(?string $visibility, string $userVisibility): string
    {
        //Public
        if ($visibility === VisibilityEnum::VISIBILITY_PUBLIC && $userVisibility === VisibilityEnum::VISIBILITY_PUBLIC) {
            return $this->translator->trans('global.visibilities.reason.both_public');
        }

        //Private
        if ($visibility === VisibilityEnum::VISIBILITY_PRIVATE && $userVisibility === VisibilityEnum::VISIBILITY_PRIVATE) {
            return $this->translator->trans('global.visibilities.reason.both_private');
        }

        if ($userVisibility === VisibilityEnum::VISIBILITY_PRIVATE) {
            return $this->translator->trans('global.visibilities.reason.user_private');
        }

        if ($visibility === VisibilityEnum::VISIBILITY_PRIVATE) {
            return $this->translator->trans('global.visibilities.reason.object_private');
        }

        //Internal
        if ($visibility === VisibilityEnum::VISIBILITY_INTERNAL && $userVisibility === VisibilityEnum::VISIBILITY_INTERNAL) {
            return $this->translator->trans('global.visibilities.reason.both_internal');
        }

        if ($userVisibility === VisibilityEnum::VISIBILITY_INTERNAL) {
            return $this->translator->trans('global.visibilities.reason.user_internal');
        }

        if ($visibility === VisibilityEnum::VISIBILITY_INTERNAL) {
            return $this->translator->trans('global.visibilities.reason.object_internal');
        }

        return '';
    }
}