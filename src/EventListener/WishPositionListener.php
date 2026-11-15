<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Wish;
use App\Repository\WishRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
final readonly class WishPositionListener
{
    public function __construct(
        private WishRepository $wishRepository
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Wish || $entity->getPosition() !== null || $entity->getWishlist() === null) {
            return;
        }

        $entity->setPosition($this->wishRepository->findNextPosition($entity->getWishlist()));
    }
}
