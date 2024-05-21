<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\User;
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
use Symfony\Bundle\SecurityBundle\Security;
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
        private readonly Security $security
    ) {
    }

    public function refreshAllCaches(OutputInterface $output = null): void
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
                'publicCounters' => [
                    'children' => $this->collectionRepository->count(['parent' => $collection->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PUBLIC]),
                    'items' => $this->itemRepository->count(['collection' => $collection->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PUBLIC]),
                ],
                'internalCounters' => [
                    'children' => $this->collectionRepository->count(['parent' => $collection->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_INTERNAL]),
                    'items' => $this->itemRepository->count(['collection' => $collection->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_INTERNAL]),
                ],
                'privateCounters' => [
                    'children' => $this->collectionRepository->count(['parent' => $collection->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PRIVATE]),
                    'items' => $this->itemRepository->count(['collection' => $collection->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PRIVATE]),
                ]
            ],
            'prices' =>[
                'publicPrices' => $this->datumRepository->computePricesForCollection($collection, VisibilityEnum::VISIBILITY_PUBLIC),
                'internalPrices' => $this->datumRepository->computePricesForCollection($collection, VisibilityEnum::VISIBILITY_INTERNAL),
                'privatePrices' => $this->datumRepository->computePricesForCollection($collection, VisibilityEnum::VISIBILITY_PRIVATE),
            ],
        ];

        foreach ($collection->getChildren() as $child) {
            $nestedCounters = $this->computeForCollection($child);
            $values['counters']['publicCounters']['children'] += $nestedCounters['counters']['publicCounters']['children'];
            $values['counters']['publicCounters']['items'] += $nestedCounters['counters']['publicCounters']['items'];

            $values['counters']['internalCounters']['children'] += $nestedCounters['counters']['internalCounters']['children'];
            $values['counters']['internalCounters']['items'] += $nestedCounters['counters']['internalCounters']['items'];

            $values['counters']['privateCounters']['children'] += $nestedCounters['counters']['privateCounters']['children'];
            $values['counters']['privateCounters']['items'] += $nestedCounters['counters']['privateCounters']['items'];


            foreach ($nestedCounters['prices']['publicPrices'] as $label => $value) {
                if (isset($values['prices']['publicPrices'][$label])) {
                    $values['prices']['publicPrices'][$label] += $value;
                } else {
                    $values['prices']['publicPrices'][$label] = $value;
                }
            }

            foreach ($nestedCounters['prices']['privateCounters'] as $label => $value) {
                if (isset($values['prices']['privateCounters'][$label])) {
                    $values['prices']['privateCounters'][$label] += $value;
                } else {
                    $values['prices']['privateCounters'][$label] = $value;
                }
            }

            foreach ($nestedCounters['prices']['internalCounters'] as $label => $value) {
                if (isset($values['prices']['internalCounters'][$label])) {
                    $values['prices']['internalCounters'][$label] += $value;
                } else {
                    $values['prices']['internalCounters'][$label] = $value;
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
                'publicCounters' => [
                    'children' => $this->wishlistRepository->count(['parent' => $wishlist->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PUBLIC]),
                    'wishes' => $this->wishRepository->count(['wishlist' => $wishlist->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PUBLIC]),
                ],
                'internalCounters' => [
                    'children' => $this->wishlistRepository->count(['parent' => $wishlist->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_INTERNAL]),
                    'wishes' => $this->wishRepository->count(['wishlist' => $wishlist->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_INTERNAL]),
                ],
                'privateCounters' => [
                    'children' => $this->wishlistRepository->count(['parent' => $wishlist->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PRIVATE]),
                    'wishes' => $this->wishRepository->count(['wishlist' => $wishlist->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PRIVATE]),
                ]
            ],
        ];

        foreach ($wishlist->getChildren() as $child) {
            $nestedCounters = $this->computeForWishlist($child);
            $values['counters']['publicCounters']['children'] += $nestedCounters['counters']['publicCounters']['children'];
            $values['counters']['publicCounters']['wishes'] += $nestedCounters['counters']['publicCounters']['wishes'];

            $values['counters']['internalCounters']['children'] += $nestedCounters['counters']['internalCounters']['children'];
            $values['counters']['internalCounters']['wishes'] += $nestedCounters['counters']['internalCounters']['wishes'];

            $values['counters']['privateCounters']['children'] += $nestedCounters['counters']['privateCounters']['children'];
            $values['counters']['privateCounters']['wishes'] += $nestedCounters['counters']['privateCounters']['wishes'];
        }

        $wishlist->setCachedValues($values);

        return $values;
    }

    public function computeForAlbum(Album $album): array
    {
        $values = [
            'counters' => [
                'publicCounters' => [
                    'children' => $this->albumRepository->count(['parent' => $album->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PUBLIC]),
                    'photos' => $this->photoRepository->count(['album' => $album->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PUBLIC]),
                ],
                'internalCounters' => [
                    'children' => $this->albumRepository->count(['parent' => $album->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_INTERNAL]),
                    'photos' => $this->photoRepository->count(['album' => $album->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_INTERNAL]),
                ],
                'privateCounters' => [
                    'children' => $this->albumRepository->count(['parent' => $album->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PRIVATE]),
                    'photos' => $this->photoRepository->count(['album' => $album->getId(), 'finalVisibility' => VisibilityEnum::VISIBILITY_PRIVATE]),
                ]
            ],
        ];

        foreach ($album->getChildren() as $child) {
            $nestedCounters = $this->computeForAlbum($child);
            $values['counters']['publicCounters']['children'] += $nestedCounters['counters']['publicCounters']['children'];
            $values['counters']['publicCounters']['photos'] += $nestedCounters['counters']['publicCounters']['photos'];

            $values['counters']['internalCounters']['children'] += $nestedCounters['counters']['internalCounters']['children'];
            $values['counters']['internalCounters']['photos'] += $nestedCounters['counters']['internalCounters']['photos'];

            $values['counters']['privateCounters']['children'] += $nestedCounters['counters']['privateCounters']['children'];
            $values['counters']['privateCounters']['photos'] += $nestedCounters['counters']['privateCounters']['photos'];
        }

        $album->setCachedValues($values);

        return $values;
    }

    public function getCachedValues(Collection|Album|Wishlist $entity): array
    {
        return match (true) {
            $entity instanceof Collection => $this->getForCollection($entity),
            $entity instanceof Album => $this->getForAlbum($entity),
            $entity instanceof Wishlist => $this->getForWishlist($entity),
        };
    }

    private function getForCollection(Collection $collection): array {
        $prices = $collection->getCachedValues()['prices']['publicPrices'];
        $counters = $collection->getCachedValues()['counters']['publicCounters'];

        if ($this->security->getUser() instanceof User) {
            $prices = $this->mergeAndAdd($prices, $collection->getCachedValues()['prices']['internalPrices']);
            $counters = $this->mergeAndAdd($counters, $collection->getCachedValues()['counters']['internalCounters']);
        }

        if ($this->security->getUser() === $collection->getOwner()) {
            $prices = $this->mergeAndAdd($prices, $collection->getCachedValues()['prices']['privatePrices']);
            $counters = $this->mergeAndAdd($counters, $collection->getCachedValues()['counters']['privateCounters']);
        }

        return [
          'prices' => $prices,
          'counters' => $counters,
        ];
    }

    private function getForAlbum(Album $album): array {
        $counters = $album->getCachedValues()['counters']['publicCounters'];

        if ($this->security->getUser() instanceof User) {
            $counters = $this->mergeAndAdd($counters, $album->getCachedValues()['counters']['internalCounters']);
        }

        if ($this->security->getUser() === $album->getOwner()) {
            $counters = $this->mergeAndAdd($counters, $album->getCachedValues()['counters']['privateCounters']);
        }

        return [
            'counters' => $counters,
        ];
    }

    private function getForWishlist(Wishlist $wishlist): array {
        $counters = $wishlist->getCachedValues()['counters']['publicCounters'];

        if ($this->security->getUser() instanceof User) {
            $counters = $this->mergeAndAdd($counters, $wishlist->getCachedValues()['counters']['internalCounters']);
        }

        if ($this->security->getUser() === $wishlist->getOwner()) {
            $counters = $this->mergeAndAdd($counters, $wishlist->getCachedValues()['counters']['privateCounters']);
        }

        return [
            'counters' => $counters,
        ];
    }

    private function mergeAndAdd(array $array1, array $array2): array
    {
        $result = [];

        foreach (array_keys($array1 + $array2) as $value) {
            $result[$value] = ($array1[$value] ?? 0) + ($array2[$value] ?? 0);
        }

        return $result;
    }
}
