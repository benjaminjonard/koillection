<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\Image;
use App\Entity\User;
use App\Service\DiskUsageChecker;
use App\Service\ImageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ImageListener
{
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    /**
     * @var ImageHandler
     */
    private ImageHandler $imageHandler;

    /**
     * @var DiskUsageChecker
     */
    private DiskUsageChecker $duc;

    /**
     * ImageListener constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param ImageHandler $imageHandler
     * @param DiskUsageChecker $duc
     */
    public function __construct(TokenStorageInterface $tokenStorage, ImageHandler $imageHandler, DiskUsageChecker $duc)
    {
        $this->tokenStorage = $tokenStorage;
        $this->imageHandler = $imageHandler;
        $this->duc = $duc;
    }

    /**
     * @param OnFlushEventArgs $args
     * @throws ORMException
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        if (!$this->tokenStorage->getToken()) {
            return;
        }

        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        $user = $this->tokenStorage->getToken()->getUser();

        $images = [];
        foreach ($uow->getScheduledEntityInsertions() as $keyEntity => $entity) {
            if ($entity instanceof Image) {
                $images[] = $entity;
            }
        }
        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Image) {
                $images[] = $entity;
            }
        }
        if ($user instanceof User) {
            $this->duc->hasEnoughSpaceForUpload($user, $images);
        }

        foreach ($uow->getScheduledEntityInsertions() as $keyEntity => $entity) {
            if ($entity instanceof Image) {
                $this->upload($entity, $user, $em);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Image) {
                $this->remove($entity, $em); //Will still contain all data related to the previous image
                $this->upload($entity, $user, $em); // Will update the Image with new file data
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $keyEntity => $entity) {
            if ($entity instanceof Image) {
                $this->remove($entity, $em);
            }
        }
    }

    private function upload(Image $image, User $user, EntityManagerInterface $em)
    {
        $uow = $em->getUnitOfWork();

        $sizeUsed = $this->imageHandler->upload($image);
        $user->increaseDiskSpaceUsed($sizeUsed);

        $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Image::class), $image);
        $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(User::class), $user);
    }

    private function remove(Image $image, EntityManagerInterface $em)
    {
        $uow = $em->getUnitOfWork();

        $sizeFreed = $this->imageHandler->remove($image);
        $imageOwner = $image->getOwner();
        $imageOwner->decreaseDiskSpaceUsed($sizeFreed);

        $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(User::class), $imageOwner);
    }
}
