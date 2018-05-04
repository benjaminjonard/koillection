<?php

namespace App\Service\Log\Logger;

use App\Entity\Log;
use App\Entity\Tag;
use App\Enum\LogTypeEnum;
use App\Enum\VisibilityEnum;
use App\Service\Log\Logger;

/**
 * Class TagLogger
 *
 * @package App\Service\Log\Logger
 */
class TagLogger extends Logger
{
    /**
     * @return string
     */
    public function getClass() : string
    {
        return Tag::class;
    }

    /**
     * @return int
     */
    public function getPriority() : int
    {
        return 1;
    }

    /**
     * @param $tag
     * @return Log|null
     */
    public function getCreateLog($tag) : ?Log
    {
        if (!$this->supportedClass(\get_class($tag))) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_CREATED, $tag);
    }

    /**
     * @param $tag
     * @return Log|null
     */
    public function getDeleteLog($tag) : ?Log
    {
        if (!$this->supportedClass(\get_class($tag))) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_DELETED, $tag);
    }

    /**
     * @param $tag
     * @param array $changeset
     * @param array $relations
     * @return Log|null
     */
    public function getUpdateLog($tag, array $changeset, array $relations = []) : ?Log
    {
        if (!$this->supportedClass(\get_class($tag))) {
            return null;
        }

        $mainPayload = [];

        if (array_key_exists('label', $changeset)) {
            $mainPayload[] = [
                'label' => $tag->getLabel(),
                'property' => 'label',
                'old' => $changeset['label'][0],
                'new' => $tag->getLabel()
            ];
        }

        if (array_key_exists('description', $changeset)) {
            $mainPayload[] = [
                'label' => $tag->getLabel(),
                'property' => 'description',
                'old' => $changeset['description'][0],
                'new' => $tag->getDescription()
            ];
        }

        if (array_key_exists('visibility', $changeset)) {
            $mainPayload[] = [
                'label' => $tag->getLabel(),
                'property' => 'visibility',
                'old' => $changeset['visibility'][0],
                'new' => $tag->getVisibility()
            ];
        }
        if (array_key_exists('image', $changeset)) {
            $mainPayload[] = [
                'label' => $tag->getLabel(),
                'property' => 'image'
            ];
        }

        if (empty($mainPayload)) {
            return null;
        }

        return $this->createLog(
            LogTypeEnum::TYPE_UPDATED,
            $tag,
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
                return $this->translator->trans('log.tag.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['new']])."</strong>",
                    '%old%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['old']])."</strong>",
                ]);
                break;
            case 'image':
                return $this->translator->trans('log.tag.image_updated', [
                    '%property%' => "<strong>$label</strong>"
                ]);
                break;
            default:
                $defaultValue = $this->translator->trans('log.default_value');
                $old = $payload['old'] ? $payload['old'] : $defaultValue;
                $new = $payload['new'] ? $payload['new'] : $defaultValue;

                return $this->translator->trans('log.tag.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%old%' => "<strong>$old</strong>",
                    '%new%' => "<strong>$new</strong>",
                ]);
                break;
        }
    }
}
