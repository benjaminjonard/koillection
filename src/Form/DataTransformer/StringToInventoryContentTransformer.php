<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Collection;
use App\Service\InventoryHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class StringToInventoryContentTransformer implements DataTransformerInterface
{
    private InventoryHandler $inventoryHandler;

    private EntityManagerInterface $em;

    public function __construct(InventoryHandler $inventoryHandler, EntityManagerInterface $em)
    {
        $this->inventoryHandler = $inventoryHandler;
        $this->em = $em;
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
        $collections = $this->em->getRepository(Collection::class)->findAllWithItems();
        $content = $this->inventoryHandler->buildInventory($collections, $ids);

        return json_encode($content);
    }
}
