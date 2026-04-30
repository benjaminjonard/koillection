<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Wishlist;
use App\Enum\VisibilityEnum;
use App\Repository\AlbumRepository;
use App\Repository\CollectionRepository;
use App\Repository\DatumRepository;
use App\Repository\ItemRepository;
use App\Repository\PhotoRepository;
use App\Repository\WishlistRepository;
use App\Repository\WishRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Output\OutputInterface;

class CachedValuesCalculator
{
    public function __construct(
        private readonly CollectionRepository $collectionRepository,
        private readonly ItemRepository $itemRepository,
        private readonly DatumRepository $datumRepository,
        private readonly WishlistRepository $wishlistRepository,
        private readonly WishRepository $wishRepository,
        private readonly AlbumRepository $albumRepository,
        private readonly PhotoRepository $photoRepository,
        private readonly ManagerRegistry $managerRegistry,
    ) {
    }

    public function refreshAllCaches(?OutputInterface $output = null): void
    {
        $output?->writeln('Refreshing cached values for collections...');
        foreach ($this->collectionRepository->findBy(['parent' => null]) as $rootCollection) {
            $this->computeForCollection($rootCollection);
        }

        $output?->writeln('Refreshing cached values for wishlists...');
        foreach ($this->wishlistRepository->findBy(['parent' => null]) as $rootWishlist) {
            $this->computeForWishlist($rootWishlist);
        }

        $output?->writeln('Refreshing cached values for albums...');
        foreach ($this->albumRepository->findBy(['parent' => null]) as $rootAlbum) {
            $this->computeForAlbum($rootAlbum);
        }

        $this->managerRegistry->getManager()->flush();
    }

    public function computeForCollection(Collection $collection): array
    {
        $values = ['counters' => [], 'prices' => []];

        foreach (VisibilityEnum::VISIBILITIES as $visibility) {
            $countersKey = $visibility . 'Counters';
            $pricesKey = $visibility . 'Prices';

            $values['counters'][$countersKey] = [
                'children' => $this->collectionRepository->count(['parent' => $collection->getId(), 'finalVisibility' => $visibility]),
                'items' => $this->itemRepository->count(['collection' => $collection->getId(), 'finalVisibility' => $visibility]),
            ];

            $values['prices'][$pricesKey] = $this->datumRepository->computePricesForCollection($collection, $visibility);
        }

        foreach ($collection->getChildren() as $child) {
            $nested = $this->computeForCollection($child);

            foreach (VisibilityEnum::VISIBILITIES as $visibility) {
                $countersKey = $visibility . 'Counters';
                $pricesKey = $visibility . 'Prices';

                $values['counters'][$countersKey]['children'] += $nested['counters'][$countersKey]['children'];
                $values['counters'][$countersKey]['items'] += $nested['counters'][$countersKey]['items'];

                $values['prices'][$pricesKey] = $this->mergePrices(
                    $values['prices'][$pricesKey],
                    $nested['prices'][$pricesKey]
                );
            }
        }

        $collection->setCachedValues($values);

        return $values;
    }

    public function computeForWishlist(Wishlist $wishlist): array
    {
        $values = ['counters' => []];

        foreach (VisibilityEnum::VISIBILITIES as $visibility) {
            $countersKey = $visibility . 'Counters';

            $values['counters'][$countersKey] = [
                'children' => $this->wishlistRepository->count(['parent' => $wishlist->getId(), 'finalVisibility' => $visibility]),
                'wishes' => $this->wishRepository->count(['wishlist' => $wishlist->getId(), 'finalVisibility' => $visibility]),
            ];
        }

        foreach ($wishlist->getChildren() as $child) {
            $nested = $this->computeForWishlist($child);

            foreach (VisibilityEnum::VISIBILITIES as $visibility) {
                $countersKey = $visibility . 'Counters';
                $values['counters'][$countersKey]['children'] += $nested['counters'][$countersKey]['children'];
                $values['counters'][$countersKey]['wishes'] += $nested['counters'][$countersKey]['wishes'];
            }
        }

        $wishlist->setCachedValues($values);

        return $values;
    }

    public function computeForAlbum(Album $album): array
    {
        $values = ['counters' => []];

        foreach (VisibilityEnum::VISIBILITIES as $visibility) {
            $countersKey = $visibility . 'Counters';

            $values['counters'][$countersKey] = [
                'children' => $this->albumRepository->count(['parent' => $album->getId(), 'finalVisibility' => $visibility]),
                'photos' => $this->photoRepository->count(['album' => $album->getId(), 'finalVisibility' => $visibility]),
            ];
        }

        foreach ($album->getChildren() as $child) {
            $nested = $this->computeForAlbum($child);

            foreach (VisibilityEnum::VISIBILITIES as $visibility) {
                $countersKey = $visibility . 'Counters';
                $values['counters'][$countersKey]['children'] += $nested['counters'][$countersKey]['children'];
                $values['counters'][$countersKey]['photos'] += $nested['counters'][$countersKey]['photos'];
            }
        }

        $album->setCachedValues($values);

        return $values;
    }

    private function mergePrices(array $existing, array $additions): array
    {
        foreach ($additions as $label => $currencies) {
            foreach ($currencies as $currency => $value) {
                $existing[$label][$currency] = ($existing[$label][$currency] ?? 0) + $value;
            }
        }

        return $existing;
    }
}
