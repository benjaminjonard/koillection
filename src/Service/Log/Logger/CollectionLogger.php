<?php

namespace App\Service\Log\Logger;

use App\Entity\Collection;
use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Enum\VisibilityEnum;
use App\Service\Log\Logger;

/**
 * Class CollectionLogger
 *
 * @package App\Service\Log\Logger
 */
class CollectionLogger extends Logger
{
    /**
     * @return string
     */
    public function getLabelGetter() : string
    {
        return 'getTitle';
    }

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

        return $this->createLog(LogTypeEnum::TYPE_CREATED, $collection);
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

        return $this->createLog(LogTypeEnum::TYPE_DELETED, $collection);
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

        if (array_key_exists('title', $changeset)) {
            $mainPayload[] = [
                'title' => $collection->getTitle(),
                'property' => 'title',
                'old' => $changeset['title'][0],
                'new' => $collection->getTitle()
            ];
        }

        if (array_key_exists('childrenTitle', $changeset)) {
            $mainPayload[] = [
                'title' => $collection->getTitle(),
                'property' => 'childrenTitle',
                'old' => $changeset['childrenTitle'][0],
                'new' => $collection->getChildrenTitle()
            ];
        }

        if (array_key_exists('itemsTitle', $changeset)) {
            $mainPayload[] = [
                'title' => $collection->getTitle(),
                'property' => 'itemsTitle',
                'old' => $changeset['itemsTitle'][0],
                'new' => $collection->getItemsTitle()
            ];
        }

        if (array_key_exists('visibility', $changeset)) {
            $mainPayload[] = [
                'title' => $collection->getTitle(),
                'property' => 'visibility',
                'old' => $changeset['visibility'][0],
                'new' => $collection->getVisibility()
            ];
        }

        if (array_key_exists('image', $changeset)) {
            $mainPayload[] = [
                'title' => $collection->getTitle(),
                'property' => 'image'
            ];
        }

        if (array_key_exists('parent', $changeset)) {
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

        if (empty($mainPayload)) {
            return null;
        }

        return $this->createLog(
            LogTypeEnum::TYPE_UPDATED,
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
        $label =  $this->translator->trans('label.'.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property)));
        switch ($property) {
            case 'visibility':
                return $this->translator->trans('log.collection.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['new']])."</strong>",
                    '%old%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['old']])."</strong>",
                ]);
                break;
            case 'image':
                return $this->translator->trans('log.collection.image_updated', [
                    '%property%' => "<strong>$label</strong>"
                ]);
                break;
            case 'parent':
                $defaultValue = $this->translator->trans('log.collection.default_parent');
                $old = $payload['old_title'] ? $payload['old_title'] : $defaultValue;
                $new = $payload['new_title'] ? $payload['new_title'] : $defaultValue;

                return $this->translator->trans('log.collection.parent_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>$old</strong>",
                    '%old%' => "<strong>$new</strong>",
                ]);
                break;
            default:
                $defaultValue = $this->translator->trans('log.default_value');
                $old = $payload['old'] ? $payload['old'] : $defaultValue;
                $new = $payload['new'] ? $payload['new'] : $defaultValue;

                return $this->translator->trans('log.collection.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%old%' => "<strong>$old</strong>",
                    '%new%' => "<strong>$new</strong>",
                ]);
                break;
        }
    }
}
