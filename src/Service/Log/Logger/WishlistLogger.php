<?php

declare(strict_types=1);

namespace App\Service\Log\Logger;

use App\Entity\Log;
use App\Entity\Wishlist;
use App\Enum\LogTypeEnum;
use App\Enum\VisibilityEnum;
use App\Service\Log\Logger;

class WishlistLogger extends Logger
{
    /**
     * @return string
     */
    public function getClass() : string
    {
        return Wishlist::class;
    }

    /**
     * @return int
     */
    public function getPriority() : int
    {
        return 1;
    }

    /**
     * @param $wishlist
     * @return Log|null
     */
    public function getCreateLog($wishlist) : ?Log
    {
        if (!$this->supportedClass(\get_class($wishlist))) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_CREATE, $wishlist);
    }

    /**
     * @param $wishlist
     * @return Log|null
     */
    public function getDeleteLog($wishlist) : ?Log
    {
        if (!$this->supportedClass(\get_class($wishlist))) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_DELETE, $wishlist);
    }

    /**
     * @param $wishlist
     * @param array $changeset
     * @param array $relations
     * @return Log|null
     */
    public function getUpdateLog($wishlist, array $changeset, array $relations = []) : ?Log
    {
        if (!$this->supportedClass(\get_class($wishlist))) {
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

    /**
     * @param $class
     * @param array $payload
     * @return null|string
     */
    public function formatPayload($class, array $payload) : ?string
    {
        if (!$this->supportedClass($class)) {
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
