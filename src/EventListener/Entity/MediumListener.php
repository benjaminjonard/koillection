<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\Medium;
use App\Entity\User;
use App\Service\DiskUsageChecker;
use App\Service\ImageHandler;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class MediumListener
 *
 * @package App\EventListener\Entity
 */
class MediumListener
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
     * MediumListener constructor.
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

        $media = [];
        foreach ($uow->getScheduledEntityInsertions() as $keyEntity => $entity) {
            if ($entity instanceof Medium) {
                $media[] = $entity;
            }
        }
        if ($user instanceof User) {
            $this->duc->hasEnoughSpaceForUpload($user, $media);
        }

        foreach ($uow->getScheduledEntityInsertions() as $keyEntity => $entity) {
            if ($entity instanceof Medium) {
                $sizeUsed = $this->imageHandler->upload($entity);
                $user->increaseDiskSpaceUsed($sizeUsed);

                $em->persist($entity);
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Medium::class), $entity);
                $em->persist($user);
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(User::class), $user);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $keyEntity => $entity) {
            if ($entity instanceof Medium && $entity->getId()) {
                if ($entity->fileCanBeDeleted()) {
                    $sizeFreed = $this->imageHandler->remove($entity);
                    $mediumOwner = $entity->getOwner();
                    $mediumOwner->decreaseDiskSpaceUsed($sizeFreed);

                    $em->persist($entity);
                    $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Medium::class), $entity);
                    $em->persist($mediumOwner);
                    $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(User::class), $mediumOwner);
                }
            }
        }
    }
}
