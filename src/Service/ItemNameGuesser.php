<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Collection;
use App\Entity\Item;
use function PHPUnit\Framework\stringStartsWith;

class ItemNameGuesser
{
    public function guess(Item &$item): ?array
    {
        $collection = $item->getCollection();
        if (!$collection instanceof Collection || $collection->getItems()->count() < 1) {
            return null;
        }

        $patternParts = preg_split('/\d+/', $collection->getItems()->first()->getName());
        if (empty($patternParts) || \count($patternParts) > 2) {
            return null;
        }
        $pattern = implode('', $patternParts);

        $highestValue = 0;
        foreach ($collection->getItems() as $otherItem) {
            $value = mb_substr($otherItem->getName(), mb_strlen($pattern));

            if (!is_numeric($value)) {
                return null;
            }

            if ($value > $highestValue) {
                $highestValue = $value;
            }
        }

        return [implode((string) ($highestValue + 1), $patternParts)];
    }
}
