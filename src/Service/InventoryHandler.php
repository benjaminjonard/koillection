<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Collection;
use App\Entity\Inventory;

/**
 * Class InventoryHandler
 *
 * @package App\Service
 */
class InventoryHandler
{
    /**
     * @param array $collections
     * @param array $collectionIdsToAddInInventory
     * @return array
     */
    public function buildInventory(array $collections, array $collectionIdsToAddInInventory)
    {
        $content = [];

        foreach ($collections as $collection) {
            if ($collection->getParent() === null) {
                $element = $this->buildCollection($collection, $collectionIdsToAddInInventory);
                if ($element !== null) {
                    $content[] = $element;
                }
            }
        }

        return $content;
    }

    /**
     * @param Collection $collection
     * @param array $collectionIdsToAddInInventory
     * @return array|null
     */
    public function buildCollection(Collection $collection, array $collectionIdsToAddInInventory)
    {
        $element = null;

        if (\in_array($collection->getId(), $collectionIdsToAddInInventory)) {
            $element = [
                'id' => $collection->getId(),
                'title' => $collection->getTitle(),
                'children' => [],
                'items' => []
            ];

            foreach ($collection->getItems() as $item) {
                $element['items'][] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'checked' => false
                ];
            }
        }

        foreach ($collection->getChildren() as $child) {
            $childElement = $this->buildCollection($child, $collectionIdsToAddInInventory);
            if ($childElement !== null) {
                if ($element === null) {
                    $element = [
                        'id' => $collection->getId(),
                        'title' => $collection->getTitle(),
                        'items' => []
                    ];
                }

                $element['children'][] = $childElement;
            }
        }

        return $element;
    }

    public function setCheckedValues(Inventory $inventory, array $itemIds)
    {
        $content = $inventory->getContent();
        foreach ($itemIds as $itemId => $checked) {
            $content = preg_replace('/([^.]*{"id":"' . $itemId . '","name":")([^.]*?","checked":)(false|true)/is', '$1$2'.$checked, $content);
        };

        $inventory->setContent($content);

        return $inventory;
    }
}
