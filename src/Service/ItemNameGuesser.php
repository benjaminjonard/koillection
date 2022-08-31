<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Item;

class ItemNameGuesser
{
    public function guess(Item &$item): ?array
    {
        $collection = $item->getCollection();
        if (!$collection instanceof \App\Entity\Collection || $collection->getItems()->count() < 1) {
            return null;
        }

        $patternParts = preg_split('/\d+/', $collection->getItems()->first()->getName());
        if (empty($patternParts) || \count($patternParts) > 2) {
            return null;
        }

        $pattern = '/'.implode('(\d+)', $patternParts).'/';

        $highestValue = 0;
        foreach ($collection->getItems() as $otherItem) {
            if (!preg_match($pattern, $otherItem->getName(), $matches) || !isset($matches[1])) {
                return null;
            }
            if ($matches[1] > $highestValue) {
                $highestValue = $matches[1];
            }
        }

        return [implode((string) ($highestValue + 1), $patternParts)];
    }
}
