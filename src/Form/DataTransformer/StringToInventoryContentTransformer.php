<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Repository\CollectionRepository;
use App\Service\InventoryHandler;
use Symfony\Component\Form\DataTransformerInterface;

class StringToInventoryContentTransformer implements DataTransformerInterface
{
    private InventoryHandler $inventoryHandler;

    private CollectionRepository $collectionRepository;

    public function __construct(InventoryHandler $inventoryHandler, CollectionRepository $collectionRepository)
    {
        $this->inventoryHandler = $inventoryHandler;
        $this->collectionRepository = $collectionRepository;
    }

    public function transform($content)
    {
        return '';
    }

    public function reverseTransform($string)
    {
        if ($string === null) {
            return json_encode([]);
        }

        $ids = explode(',', $string);
        $collections = $this->collectionRepository->findAllWithItems();
        $content = $this->inventoryHandler->buildInventory($collections, $ids);

        return json_encode($content);
    }
}
