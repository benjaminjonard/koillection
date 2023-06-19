<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Attribute\UploadAnnotationReader;
use App\Service\ImageHandler;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postRemove)]
#[AsDoctrineListener(event: Events::postLoad)]
final readonly class UploadListener
{
    private PropertyAccessor $accessor;

    public function __construct(
        private UploadAnnotationReader $reader,
        private ImageHandler $handler
    ) {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        foreach ($this->reader->getUploadFields($entity) as $property => $attribute) {
            $this->handler->upload($entity, $property, $attribute);
        }
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            foreach ($this->reader->getUploadFields($entity) as $property => $attribute) {
                // New uploaded file
                $this->handler->upload($entity, $property, $attribute);

                // File deleted from a Form
                if (null !== $attribute->getDeleteProperty() && true === $this->accessor->getValue($entity, $attribute->getDeleteProperty())) {
                    $this->handler->removeOldFile($entity, $attribute);
                }

                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata($entity::class), $entity);
            }
        }
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();

        foreach ($this->reader->getUploadFields($entity) as $property => $attribute) {
            $this->handler->setFileFromFilename($entity, $property, $attribute);
        }
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        foreach ($this->reader->getUploadFields($entity) as $attribute) {
            $this->handler->removeOldFile($entity, $attribute);
        }
    }
}
