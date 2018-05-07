<?php

namespace App\Service\Log\Logger;

use App\Entity\Item;
use App\Entity\Log;
use App\Entity\Tag;
use App\Enum\DatumTypeEnum;
use App\Enum\LogTypeEnum;
use App\Enum\VisibilityEnum;
use App\Service\Log\Logger;

/**
 * Class ItemLogger
 *
 * @package App\Service\Log\Logger
 */
class ItemLogger extends Logger
{
    /**
     * @return string
     */
    public function getClass() : string
    {
        return Item::class;
    }

    /**
     * @return int
     */
    public function getPriority() : int
    {
        return 1;
    }

    /**
     * @param $item
     * @return Log|null
     */
    public function getCreateLog($item) : ?Log
    {
        if (!$this->supportedClass(\get_class($item))) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_CREATED, $item);
    }

    /**
     * @param $item
     * @return Log|null
     */
    public function getDeleteLog($item) : ?Log
    {
        if (!$this->supportedClass(\get_class($item))) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_DELETED, $item);
    }

    /**
     * @param $item
     * @param array $changeset
     * @param array $relations
     * @return Log|null
     */
    public function getUpdateLog($item, array $changeset, array $relations = []) : ?Log
    {
        if (!$this->supportedClass(\get_class($item))) {
            return null;
        }

        $mainPayload = [];
        foreach ($changeset as $property => $change) {
            if (in_array($property, ['name', 'quantity', 'visibility'])) {
                $function = 'get'.ucfirst($property);
                $mainPayload[] = [
                    'name' => $item->getName(),
                    'property' => $property,
                    'old' => $changeset[$property][0],
                    'new' => $item->$function()
                ];
            } elseif ($property === 'image') {
                $mainPayload[] = [
                    'name' => $item->getName(),
                    'property' => 'image'
                ];
            } elseif ($property === 'collection') {
                $old = $changeset['collection'][0];
                $new = $item->getCollection();

                $mainPayload[] = [
                    'property' => 'collection',
                    'old_id' => $old->getId(),
                    'old_title' => $old->getTitle(),
                    'new_id' => $new->getId(),
                    'new_title' => $new->getTitle(),
                    'name' => $item->getName()
                ];
            }
        }

        foreach ($relations['added'] as $relation) {
            if ($relation instanceof Tag) {
                $mainPayload[] = [
                    'name' => $item->getName(),
                    'property' => 'tag_added',
                    'tag_label' => $relation->getLabel(),
                    'tag_id' => $relation->getId()
                ];
            }
        }

        foreach ($relations['deleted'] as $relation) {
            if ($relation instanceof Tag) {
                $mainPayload[] = [
                    'name' => $item->getName(),
                    'property' => 'tag_removed',
                    'tag_label' => $relation->getLabel(),
                    'tag_id' => $relation->getId()
                ];
            }
        }

        if (empty($mainPayload)) {
            return null;
        }

        return $this->createLog(
            LogTypeEnum::TYPE_UPDATED,
            $item,
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
        $label = null;
        if (!\in_array($property, ['tag_added', 'tag_removed'], false)) {
            $label = $this->translator->trans('label.'.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property)));
        }

        switch ($property) {
            case 'visibility':
                return $this->translator->trans('log.item.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['new']])."</strong>",
                    '%old%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['old']])."</strong>",
                ]);
            case 'image':
                return $this->translator->trans('log.item.image_updated', [
                    '%property%' => "<strong>$label</strong>"
                ]);
            case 'collection':
                $old = $payload['old_title'];
                $new = $payload['new_title'];

                return $this->translator->trans('log.item.collection_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>$old</strong>",
                    '%old%' => "<strong>$new</strong>",
                ]);
            case 'tag_added':
                return $this->translator->trans('log.item.tag_added', [
                    '%tag%' => "<strong>".$payload['tag_label']."</strong>"
                ]);
                break;
            case 'tag_removed':
                return $this->translator->trans('log.item.tag_removed', [
                    '%tag%' => "<strong>".$payload['tag_label']."</strong>"
                ]);
                break;
            case 'datum_added':
                switch ($payload['datum_type']) {
                    case DatumTypeEnum::TYPE_IMAGE:
                        return $this->translator->trans('log.item.image_added', [
                            '%label%' => "<strong>".$payload['datum_label']."</strong>"
                        ]);
                    case DatumTypeEnum::TYPE_SIGN: {
                        return $this->translator->trans('log.item.sign_added', [
                            '%label%' => "<strong>".$payload['datum_label']."</strong>",
                            '%value%' => "<strong>".$payload['datum_value']."</strong>"
                        ]);
                    }
                    default:
                        return $this->translator->trans('log.item.property_added', [
                            '%label%' => "<strong>".$payload['datum_label']."</strong>",
                            '%value%' => "<strong>".$payload['datum_value']."</strong>"
                        ]);
                }
            case 'datum_removed':
                switch ($payload['datum_type']) {
                    case DatumTypeEnum::TYPE_IMAGE:
                        return $this->translator->trans('log.item.image_removed', [
                            '%label%' => "<strong>".$payload['datum_label']."</strong>"
                        ]);
                    case DatumTypeEnum::TYPE_SIGN: {
                        return $this->translator->trans('log.item.sign_removed', [
                            '%label%' => "<strong>".$payload['datum_label']."</strong>",
                            '%value%' => "<strong>".$payload['datum_value']."</strong>"
                        ]);
                    }
                    default:
                        return $this->translator->trans('log.item.property_removed', [
                            '%label%' => "<strong>".$payload['datum_label']."</strong>",
                            '%value%' => "<strong>".$payload['datum_value']."</strong>"
                        ]);
                }
            default:
                $defaultValue = $this->translator->trans('log.default_value');
                $old = $payload['old'] ? $payload['old'] : $defaultValue;
                $new = $payload['new'] ? $payload['new'] : $defaultValue;

                return $this->translator->trans('log.item.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%old%' => "<strong>$old</strong>",
                    '%new%' => "<strong>$new</strong>",
                ]);
        }
    }
}
