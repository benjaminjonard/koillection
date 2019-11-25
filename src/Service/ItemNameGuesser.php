<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\Template;
use App\Enum\DatumTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ItemNameGuesser
 *
 * @package App\Service
 */
class ItemNameGuesser
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * ItemHelper constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Item $item
     * @return string|null
     */
    public function guess(Item &$item) : ?array
    {
        $collection = $item->getCollection();
        if ($collection === null || $collection->getItems()->count() < 1) {
            return null;
        }

        $patternParts = preg_split('/\d+/', $collection->getItems()->first()->getName());
        if (empty($patternParts) || \count($patternParts) > 2) {
            return null;
        }

        $pattern = '/' . implode('(\d+)', $patternParts) . '/';

        $highestValue = 0;
        foreach ($collection->getItems() as $otherItem) {
            if (!preg_match($pattern, $otherItem->getName(), $matches) || !isset($matches[1])) {
                return null;
            }
            if ($matches[1] > $highestValue) {
                $highestValue = $matches[1];
            }
        }

        return [implode($highestValue + 1, $patternParts)];
    }
}
