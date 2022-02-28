<?php

declare(strict_types=1);

namespace App\Service\Log\Logger;

use App\Entity\Album;
use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Enum\VisibilityEnum;
use App\Service\Log\Logger;

class AlbumLogger extends Logger
{
    public function getClass(): string
    {
        return Album::class;
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function getCreateLog(LoggableInterface $album): ?Log
    {
        if (!$this->supports($album)) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_CREATE, $album);
    }

    public function getDeleteLog(LoggableInterface $album): ?Log
    {
        if (!$this->supports($album)) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_DELETE, $album);
    }

    public function getUpdateLog(LoggableInterface $album, array $changeset, array $relations = []): ?Log
    {
        if (!$this->supports($album)) {
            return null;
        }
        $mainPayload = [];
        foreach ($changeset as $property => $change) {
            if (\in_array($property, ['title', 'visibility'])) {
                $function = 'get'.ucfirst($property);
                $mainPayload[] = [
                    'title' => $album->getTitle(),
                    'property' => $property,
                    'old' => $changeset[$property][0],
                    'new' => $album->$function()
                ];
            } elseif ($property === 'image') {
                $mainPayload[] = [
                    'title' => $album->getTitle(),
                    'property' => 'image'
                ];
            } elseif ($property === 'parent') {
                $old = $changeset['parent'][0] instanceof Album ? $changeset['parent'][0] : null;
                $new = $album->getParent() instanceof Album ? $album->getParent() : null;

                $mainPayload[] = [
                    'property' => 'parent',
                    'old_id' => $old ? $old->getId() : null,
                    'old_title' => $old ? $old->getTitle() : null,
                    'new_id' => $new ? $new->getId() : null,
                    'new_title' => $new ? $new->getTitle() : null,
                    'title' => $album->getTitle()
                ];
            }
        }

        if (empty($mainPayload)) {
            return null;
        }

        return $this->createLog(
            LogTypeEnum::TYPE_UPDATE,
            $album,
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
                return $this->translator->trans('log.album.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['new']])."</strong>",
                    '%old%' => "<strong>".$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['old']])."</strong>",
                ]);
            case 'image':
                return $this->translator->trans('log.album.image_updated', [
                    '%property%' => "<strong>$label</strong>"
                ]);
            case 'parent':
                $defaultValue = $this->translator->trans('log.album.default_parent');
                $old = $payload['old_title'] ? $payload['old_title'] : $defaultValue;
                $new = $payload['new_title'] ? $payload['new_title'] : $defaultValue;

                return $this->translator->trans('log.album.parent_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>$old</strong>",
                    '%old%' => "<strong>$new</strong>",
                ]);
            default:
                $defaultValue = $this->translator->trans('log.default_value');
                $old = $payload['old'] ? $payload['old'] : $defaultValue;
                $new = $payload['new'] ? $payload['new'] : $defaultValue;

                return $this->translator->trans('log.album.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%old%' => "<strong>$old</strong>",
                    '%new%' => "<strong>$new</strong>",
                ]);
        }
    }
}
