<?php

declare(strict_types=1);

namespace App\Service\Log\Logger;

use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use App\Entity\Wishlist;
use App\Enum\LogTypeEnum;
use App\Enum\VisibilityEnum;
use App\Service\Log\Logger;

class WishlistLogger extends Logger
{
    public function getClass(): string
    {
        return Wishlist::class;
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function getCreateLog(LoggableInterface $wishlist): ?Log
    {
        if (!$this->supports($wishlist)) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_CREATE, $wishlist);
    }

    public function getDeleteLog(LoggableInterface $wishlist): ?Log
    {
        if (!$this->supports($wishlist)) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_DELETE, $wishlist);
    }

    public function getUpdateLog(LoggableInterface $wishlist, array $changeset, array $relations = []): ?Log
    {
        if (!$this->supports($wishlist)) {
            return null;
        }
        $mainPayload = [];
        foreach ($changeset as $property => $change) {
            if (\in_array($property, ['name', 'visibility'])) {
                $function = 'get'.ucfirst($property);
                $mainPayload[] = [
                    'title' => $wishlist->getName(),
                    'property' => $property,
                    'old' => $changeset[$property][0],
                    'new' => $wishlist->$function()
                ];
            } elseif ($property === 'image') {
                $mainPayload[] = [
                    'title' => $wishlist->getName(),
                    'property' => 'image'
                ];
            } elseif ($property === 'parent') {
                $old = $changeset['parent'][0] instanceof Wishlist ? $changeset['parent'][0] : null;
                $new = $wishlist->getParent() instanceof Wishlist ? $wishlist->getParent() : null;

                $mainPayload[] = [
                    'property' => 'parent',
                    'old_id' => $old ? $old->getId() : null,
                    'old_title' => $old ? $old->getName() : null,
                    'new_id' => $new ? $new->getId() : null,
                    'new_title' => $new ? $new->getName() : null,
                    'title' => $wishlist->getName()
                ];
            }
        }

        if (empty($mainPayload)) {
            return null;
        }

        return $this->createLog(
            LogTypeEnum::TYPE_UPDATE,
            $wishlist,
            $mainPayload
        );
    }

    public function formatPayload(string $class, array $payload): ?string
    {
        if (!$this->supportsClass($class)) {
            return null;
        }

        $property = $payload['property'];
        $label = $this->translator->trans('label.'.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property)));
        switch ($property) {
            case 'visibility':
                return $this->translator->trans('log.wishlist.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['new']])."</strong>",
                    '%old%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['old']])."</strong>",
                ]);
            case 'image':
                return $this->translator->trans('log.wishlist.image_updated', [
                    '%property%' => "<strong>$label</strong>"
                ]);
            case 'parent':
                $defaultValue = $this->translator->trans('log.wishlist.default_parent');
                $old = $payload['old_title'] ? $payload['old_title'] : $defaultValue;
                $new = $payload['new_title'] ? $payload['new_title'] : $defaultValue;

                return $this->translator->trans('log.wishlist.parent_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>$old</strong>",
                    '%old%' => "<strong>$new</strong>",
                ]);
            default:
                $defaultValue = $this->translator->trans('log.default_value');
                $old = $payload['old'] ? $payload['old'] : $defaultValue;
                $new = $payload['new'] ? $payload['new'] : $defaultValue;

                return $this->translator->trans('log.wishlist.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%old%' => "<strong>$old</strong>",
                    '%new%' => "<strong>$new</strong>",
                ]);
        }
    }
}
