<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Collection;
use App\Entity\Inventory;

class InventoryHandler
{
    public function buildInventory(array $collections, array $collectionIdsToAddInInventory)
    {
        $content = [];

        foreach ($collections as $collection) {
            if (null === $collection->getParent()) {
                $element = $this->buildCollection($collection, $collectionIdsToAddInInventory);
                if (null !== $element) {
                    $content[] = $element;
                }
            }
        }

        $content = $this->computeCheckedValues($content);

        return $content;
    }

    public function buildCollection(Collection $collection, array $collectionIdsToAddInInventory)
    {
        $element = null;

        if (\in_array($collection->getId(), $collectionIdsToAddInInventory)) {
            $element = [
                'id' => $collection->getId(),
                'title' => $collection->getTitle(),
                'children' => [],
                'items' => [],
                'totalItems' => 0,
                'totalCheckedItems' => 0,
            ];

            foreach ($collection->getItems() as $item) {
                $element['items'][] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'checked' => false,
                ];

                ++$element['totalItems'];
            }
        }

        foreach ($collection->getChildren() as $child) {
            $childElement = $this->buildCollection($child, $collectionIdsToAddInInventory);
            if (null !== $childElement) {
                if (null === $element) {
                    $element = [
                        'id' => $collection->getId(),
                        'title' => $collection->getTitle(),
                        'items' => [],
                        'totalItems' => 0,
                        'totalCheckedItems' => 0,
                    ];
                }

                $element['totalItems'] = $element['totalItems'] + $childElement['totalItems'];
                $element['children'][] = $childElement;
            }
        }

        return $element;
    }

    public function setCheckedValue(Inventory $inventory, string $id, string $checked)
    {
        $content = $inventory->getContent();
        $content = preg_replace('/([^.]*{"id":"'.$id.'","name":")([^.]*?","checked":)(false|true)/is', '$1$2'.$checked, $content);

        $content = $this->computeCheckedValues(json_decode($content, true));
        $inventory->setContent(json_encode($content));

        return $inventory;
    }

    private function computeCheckedValues(array $content): array
    {
        foreach ($content as &$collection) {
            $collection['totalCheckedItems'] = $this->getCheckedItems($collection);
        }

        return $content;
    }

    private function getCheckedItems(array &$collection)
    {
        $count = 0;

        foreach ($collection['items'] as $item) {
            if (true === $item['checked']) {
                ++$count;
            }
        }

        foreach ($collection['children'] as &$child) {
            $count += $this->getCheckedItems($child);
        }

        $collection['totalCheckedItems'] = $count;

        return $count;
    }
}
