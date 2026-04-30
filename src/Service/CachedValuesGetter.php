<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\User;
use App\Entity\Wishlist;
use App\Enum\VisibilityEnum;
use Symfony\Bundle\SecurityBundle\Security;

readonly class CachedValuesGetter
{
    public function __construct(
        private Security $security
    ) {
    }

    public function getCachedValues(Collection|Album|Wishlist $entity): array
    {
        $visibilities = $this->getAccessibleVisibilities($entity);
        $cached = $entity->getCachedValues();

        $counters = [];
        foreach ($visibilities as $visibility) {
            $counters = $this->mergeAndAdd($counters, $cached['counters'][$visibility . 'Counters']);
        }

        if ($entity instanceof Collection) {
            $prices = [];
            foreach ($visibilities as $visibility) {
                $prices = $this->mergeAndAdd($prices, $cached['prices'][$visibility . 'Prices']);
            }

            return [
                'prices' => $prices,
                'counters' => $counters,
            ];
        }

        return [
            'counters' => $counters,
        ];
    }

    /**
     * @return list<string>
     */
    private function getAccessibleVisibilities(Collection|Album|Wishlist $entity): array
    {
        $visibilities = [VisibilityEnum::VISIBILITY_PUBLIC];

        if ($this->security->getUser() instanceof User) {
            $visibilities[] = VisibilityEnum::VISIBILITY_INTERNAL;
        }

        if ($this->security->getUser() === $entity->getOwner()) {
            $visibilities[] = VisibilityEnum::VISIBILITY_PRIVATE;
        }

        return $visibilities;
    }

    private function mergeAndAdd(array $array1, array $array2): array
    {
        $result = $array1;

        foreach ($array2 as $key => $value) {
            if (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                $result[$key] = $this->mergeAndAdd($result[$key], $value);
            } elseif (isset($result[$key]) && (is_numeric($result[$key]) && is_numeric($value))) {
                $result[$key] += $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
