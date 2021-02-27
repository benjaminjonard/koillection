<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\Photo;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Event\OnFlushEventArgs;

class PhotoListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Photo) {
                $changeset = $uow->getEntityChangeSet($entity);

                if (\array_key_exists('album', $changeset)) {
                    if ($entity->getAlbum()->getVisibility() === VisibilityEnum::VISIBILITY_PRIVATE) {
                        $entity->setVisibility(VisibilityEnum::VISIBILITY_PRIVATE);
                    }
                }
            }
        }
    }
}
