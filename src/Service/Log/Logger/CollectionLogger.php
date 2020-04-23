<?php

declare(strict_types=1);

namespace App\Service\Log\Logger;

use App\Entity\Collection;
use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Enum\VisibilityEnum;
use App\Service\Log\Logger;

class CollectionLogger extends Logger
{
    /**
     * @return string
     */
    public function getClass() : string
    {
        return Collection::class;
    }

    /**
     * @return int
     */
    public function getPriority() : int
    {
        return 1;
    }

    /**
     * @param $collection
     * @return Log|null
     */
    public function getCreateLog($collection) : ?Log
    {
        if (!$this->supportedClass(\get_class($collection))) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_CREATE, $collection);
    }

    /**
     * @param $collection
     * @return Log|null
     */
    public function getDeleteLog($collection) : ?Log
    {
        if (!$this->supportedClass(\get_class($collection))) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_DELETE, $collection);
    }

    /**
     * @param $collection
     * @param array $changeset
     * @param array $relations
     * @return Log|null
     */
    public function getUpdateLog($collection, array $changeset, array $relations = []) : ?Log
    {
        if (!$this->supportedClass(\get_class($collection))) {
            return null;
        }
        $mainPayload = [];
        foreach ($changeset as $property => $change) {
            if (\in_array($property, ['title', 'childrenTitle', 'itemsTitle', 'visibility'])) {
                $function = 'get'.ucfirst($property);
                $mainPayload[] = [
                    'title' => $collection->getTitle(),
                    'property' => $property,
                    'old' => $changeset[$property][0],
                    'new' => $collection->$function()
                ];
            } elseif ($property === 'image') {
                $mainPayload[] = [
                    'title' => $collection->getTitle(),
                    'property' => 'image'
                ];
            } elseif ($property === 'parent') {
                $old = $changeset['parent'][0] instanceof Collection ? $changeset['parent'][0] : null;
                $new = $collection->getParent() instanceof Collection ? $collection->getParent() : null;

                $mainPayload[] = [
                    'property' => 'parent',
                    'old_id' => $old ? $old->getId() : null,
                    'old_title' => $old ? $old->getTitle() : null,
                    'new_id' => $new ? $new->getId() : null,
                    'new_title' => $new ? $new->getTitle() : null,
                    'title' => $collection->getTitle()
                ];
            }
        }

        if (empty($mainPayload)) {
            return null;
        }

        return $this->createLog(
            LogTypeEnum::TYPE_UPDATE,
            $collection,
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
                return $this->translator->trans('log.collection.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['new']])."</strong>",
                    '%old%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['old']])."</strong>",
                ]);
            case 'image':
                return $this->translator->trans('log.collection.image_updated', [
                    '%property%' => "<strong>$label</strong>"
                ]);
            case 'parent':
                $defaultValue = $this->translator->trans('log.collection.default_parent');
                $old = $payload['old_title'] ? $payload['old_title'] : $defaultValue;
                $new = $payload['new_title'] ? $payload['new_title'] : $defaultValue;

                return $this->translator->trans('log.collection.parent_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>$old</strong>",
                    '%old%' => "<strong>$new</strong>",
                ]);
            default:
                $defaultValue = $this->translator->trans('log.default_value');
                $old = $payload['old'] ? $payload['old'] : $defaultValue;
                $new = $payload['new'] ? $payload['new'] : $defaultValue;

                return $this->translator->trans('log.collection.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%old%' => "<strong>$old</strong>",
                    '%new%' => "<strong>$new</strong>",
                ]);
        }
    }
}
