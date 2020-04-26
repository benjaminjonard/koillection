<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Annotation\UploadAnnotationReader;
use App\Service\ImageHandler;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

final class UploadListener
{
    /**
     * @var UploadAnnotationReader
     */
    private UploadAnnotationReader $reader;

    /**
     * @var ImageHandler
     */
    private ImageHandler $handler;

    /**
     * UploadListener constructor.
     * @param UploadAnnotationReader $reader
     * @param ImageHandler $handler
     */
    public function __construct(UploadAnnotationReader $reader, ImageHandler $handler)
    {
        $this->reader = $reader;
        $this->handler = $handler;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        foreach ($this->reader->getUploadFields($entity) as $property => $annotation) {
            $this->handler->upload($entity, $property, $annotation);
        }
    }

    /**
     * @param OnFlushEventArgs $args
     * @throws \Exception
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            foreach ($this->reader->getUploadFields($entity) as $property => $annotation) {
                $this->handler->upload($entity, $property, $annotation);
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($entity)), $entity);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        foreach ($this->reader->getUploadFields($entity) as $property => $annotation) {
            $this->handler->setFileFromFilename($entity, $property, $annotation);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        foreach ($this->reader->getUploadFields($entity) as $annotation) {
            $this->handler->removeOldFile($entity, $annotation);
        }
    }
}
