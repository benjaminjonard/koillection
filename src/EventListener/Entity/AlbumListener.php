<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\Album;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Event\OnFlushEventArgs;

class AlbumListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Album) {
                $changeset = $uow->getEntityChangeSet($entity);
                if (\array_key_exists('parent', $changeset)) {
                    if ($entity->getParent()->getVisibility() === VisibilityEnum::VISIBILITY_PRIVATE) {
                        $this->setVisibilityRecursively($entity, $entity->getParent()->getVisibility());
                    }
                }

                if (\array_key_exists('visibility', $changeset)) {
                    $this->setVisibilityRecursively($entity, $entity->getVisibility());
                }
            }
        }
    }

    public function setVisibilityRecursively(Album $album, $visibility)
    {
        $album->setVisibility($visibility);

        foreach ($album->getPhotos() as $photo) {
            $photo->setVisibility($visibility);
        }

        foreach ($album->getChildren() as $child) {
            $this->setVisibilityRecursively($child, $visibility);
        }
    }
}
