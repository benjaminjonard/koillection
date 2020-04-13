<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\Image;
use App\Entity\User;
use App\Service\DiskUsageChecker;
use App\Service\ImageHandler;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ImageListener
 *
 * @package App\EventListener\Entity
 */
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
        if ($user instanceof User) {
            $this->duc->hasEnoughSpaceForUpload($user, $images);
        }

        foreach ($uow->getScheduledEntityInsertions() as $keyEntity => $entity) {
            if ($entity instanceof Image) {
                $sizeUsed = $this->imageHandler->upload($entity);
                $user->increaseDiskSpaceUsed($sizeUsed);

                $em->persist($entity);
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Image::class), $entity);
                $em->persist($user);
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(User::class), $user);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $keyEntity => $entity) {
            if ($entity instanceof Image && $entity->getId()) {
                if ($entity->fileCanBeDeleted()) {
                    $sizeFreed = $this->imageHandler->remove($entity);
                    $iamgeOwner = $entity->getOwner();
                    $iamgeOwner->decreaseDiskSpaceUsed($sizeFreed);

                    $em->persist($entity);
                    $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Image::class), $entity);
                    $em->persist($iamgeOwner);
                    $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(User::class), $iamgeOwner);
                }
            }
        }
    }
}
