<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Wishlist;
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
        $values = [
            'counters' => [
                'children' => $this->collectionRepository->count(['parent' => $collection->getId()]),
                'items' => $this->itemRepository->count(['collection' => $collection->getId()]),
            ],
            'prices' => $this->datumRepository->computePricesForCollection($collection),
        ];

        foreach ($collection->getChildren() as $child) {
            $nestedCounters = $this->computeForCollection($child);
            $values['counters']['children'] += $nestedCounters['counters']['children'];
            $values['counters']['items'] += $nestedCounters['counters']['items'];
            foreach ($nestedCounters['prices'] as $label => $value) {
                if (isset($values['prices'][$label])) {
                    $values['prices'][$label] += $value;
                } else {
                    $values['prices'][$label] = $value;
                }
            }
        }

        $collection->setCachedValues($values);

        return $values;
    }

    public function computeForWishlist(Wishlist $wishlist): array
    {
        $values = [
            'counters' => [
                'children' => $this->wishlistRepository->count(['parent' => $wishlist->getId()]),
                'wishes' => $this->wishRepository->count(['wishlist' => $wishlist->getId()]),
            ],
        ];

        foreach ($wishlist->getChildren() as $child) {
            $nestedCounters = $this->computeForWishlist($child);
            $values['counters']['children'] += $nestedCounters['counters']['children'];
            $values['counters']['wishes'] += $nestedCounters['counters']['wishes'];
        }

        $wishlist->setCachedValues($values);

        return $values;
    }

    public function computeForAlbum(Album $album): array
    {
        $values = [
            'counters' => [
                'children' => $this->albumRepository->count(['parent' => $album->getId()]),
                'photos' => $this->photoRepository->count(['album' => $album->getId()]),
            ],
        ];

        foreach ($album->getChildren() as $child) {
            $nestedCounters = $this->computeForAlbum($child);
            $values['counters']['children'] += $nestedCounters['counters']['children'];
            $values['counters']['photos'] += $nestedCounters['counters']['photos'];
        }

        $album->setCachedValues($values);

        return $values;
    }
}
