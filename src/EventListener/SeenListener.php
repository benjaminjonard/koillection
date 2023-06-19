<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: 'kernel.response')]
final readonly class SeenListener
{
    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $route = $event->getRequest()->get('_route');

        [$type, $id] = match ($route) {
            'app_item_show', 'app_shared_item_show' => ['item', $event->getRequest()->get('id')],
            'app_tag_item_show', 'app_shared_tag_item_show' => ['item', $event->getRequest()->get('itemId')],
            'app_collection_show', 'app_shared_collection_show' => ['collection', $event->getRequest()->get('id')],
            'app_tag_show', 'app_shared_tag_show' => ['tag', $event->getRequest()->get('id')],
            'app_album_show', 'app_shared_album_show' => ['album', $event->getRequest()->get('id')],
            'app_wishlist_show', 'app_shared_wishlist_show' => ['wishlist', $event->getRequest()->get('id')],
            default => [null, null]
        };

        if ($type) {
            $sql = "UPDATE koi_{$type} SET seen_counter = seen_counter + 1 WHERE id = ?";
            $stmt = $this->managerRegistry->getManager()->getConnection()->prepare($sql);
            $stmt->bindParam(1, $id);
            $stmt->execute();
        }
    }
}
