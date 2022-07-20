<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Repository\CollectionRepository;
use App\Service\InventoryHandler;
use Symfony\Component\Form\DataTransformerInterface;

class StringToInventoryContentTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly InventoryHandler $inventoryHandler,
        private readonly CollectionRepository $collectionRepository
    ) {
    }

    public function transform($content): string
    {
        return '';
    }

    public function reverseTransform($string): string|bool
    {
        if (null === $string) {
            return json_encode([]);
        }

        $ids = explode(',', $string);
        $collections = $this->collectionRepository->findAllWithItems();
        $content = $this->inventoryHandler->buildInventory($collections, $ids);

        return json_encode($content);
    }
}
