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
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use App\Repository\WishRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        private readonly UserRepository $userRepository,
        private readonly ManagerRegistry $managerRegistry,
    ) {
    }

    public function refreshAllCaches(?OutputInterface $output = null): void
    {
        $users = $this->userRepository->findAll();

        $output?->writeln('Refreshing cached values for collections...');
        foreach ($users as $user) {
            $this->computeCollections($user, null);
        }

        $output?->writeln('Refreshing cached values for wishlists...');
        foreach ($users as $user) {
            $this->computeWishlists($user, null);
        }

        $output?->writeln('Refreshing cached values for albums...');
        foreach ($users as $user) {
            $this->computeAlbums($user, null);
        }

        $this->managerRegistry->getManager()->flush();
    }

    /**
     * @param iterable<Album|Collection|Wishlist> $roots
     */
    public function compute(iterable $roots): void
    {
        $groups = [];
        foreach ($roots as $root) {
            $type = match (true) {
                $root instanceof Collection => Collection::class,
                $root instanceof Wishlist => Wishlist::class,
                $root instanceof Album => Album::class,
            };

            $owner = $root->getOwner();
            $groups[$type][$owner->getId()]['owner'] = $owner;
            $groups[$type][$owner->getId()]['rootIds'][] = $root->getId();
        }

        foreach ($groups[Collection::class] ?? [] as $group) {
            $this->computeCollections($group['owner'], $group['rootIds']);
        }

        foreach ($groups[Wishlist::class] ?? [] as $group) {
            $this->computeWishlists($group['owner'], $group['rootIds']);
        }

        foreach ($groups[Album::class] ?? [] as $group) {
            $this->computeAlbums($group['owner'], $group['rootIds']);
        }
    }

    private function computeCollections(User $owner, ?array $rootIds): void
    {
        $this->computeTree(
            $this->collectionRepository,
            $this->collectionRepository->findTreeForCachedValues($owner),
            $rootIds,
            fn (array $ids): array => $this->itemRepository->countForCachedValues($ids),
            'items',
            true
        );
    }

    private function computeWishlists(User $owner, ?array $rootIds): void
    {
        $this->computeTree(
            $this->wishlistRepository,
            $this->wishlistRepository->findTreeForCachedValues($owner),
            $rootIds,
            fn (array $ids): array => $this->wishRepository->countForCachedValues($ids),
            'wishes',
            false
        );
    }

    private function computeAlbums(User $owner, ?array $rootIds): void
    {
        $this->computeTree(
            $this->albumRepository,
            $this->albumRepository->findTreeForCachedValues($owner),
            $rootIds,
            fn (array $ids): array => $this->photoRepository->countForCachedValues($ids),
            'photos',
            false
        );
    }

    private function computeTree(
        ServiceEntityRepository $repository,
        array $tree,
        ?array $rootIds,
        callable $countLoader,
        string $counterKey,
        bool $withPrices
    ): void {
        $visibilities = [];
        $children = [];
        foreach ($tree as $node) {
            $visibilities[$node['id']] = $node['visibility'];
            $children[$node['id']] ??= [];
            if ($node['parentId'] !== null) {
                $children[$node['parentId']][] = $node['id'];
            }
        }

        $rootIds ??= array_column(array_filter($tree, static fn (array $node): bool => $node['parentId'] === null), 'id');
        $ids = $this->getSubtreeIds(array_filter(array_unique($rootIds), static fn (string $id): bool => isset($visibilities[$id])), $children);
        if ($ids === []) {
            return;
        }

        $values = [];
        foreach ($ids as $id) {
            $values[$id] = $this->createEmptyValues($counterKey, $withPrices);
            foreach ($children[$id] as $childId) {
                ++$values[$id]['counters'][$visibilities[$childId] . 'Counters']['children'];
            }
        }

        foreach ($countLoader($ids) as $count) {
            $values[$count['id']]['counters'][$count['visibility'] . 'Counters'][$counterKey] += (int) $count['total'];
        }

        if ($withPrices) {
            foreach ($this->datumRepository->sumPricesForCachedValues($ids) as $price) {
                $values[$price['collectionId']]['prices'][$price['visibility'] . 'Prices'][$price['label']][$price['currency']] = (float) $price['total'];
            }
        }

        foreach (array_reverse($ids) as $id) {
            foreach ($children[$id] as $childId) {
                $values[$id] = $this->mergeValues($values[$id], $values[$childId]);
            }
        }

        foreach ($repository->findBy(['id' => $ids]) as $entity) {
            $entity->setCachedValues($values[$entity->getId()]);
        }
    }

    private function getSubtreeIds(array $rootIds, array $children): array
    {
        $ids = [];
        $stack = array_reverse(array_values($rootIds));
        while ($stack !== []) {
            $id = array_pop($stack);
            if (isset($ids[$id])) {
                continue;
            }

            $ids[$id] = $id;
            foreach (array_reverse($children[$id]) as $childId) {
                $stack[] = $childId;
            }
        }

        return array_values($ids);
    }

    private function mergeValues(array $values, array $nested): array
    {
        foreach ($values['counters'] as $countersKey => $counters) {
            foreach (array_keys($counters) as $key) {
                $values['counters'][$countersKey][$key] += $nested['counters'][$countersKey][$key];
            }
        }

        foreach (array_keys($values['prices'] ?? []) as $pricesKey) {
            $values['prices'][$pricesKey] = $this->mergePrices($values['prices'][$pricesKey], $nested['prices'][$pricesKey]);
        }

        return $values;
    }

    private function createEmptyValues(string $counterKey, bool $withPrices): array
    {
        $values = ['counters' => []];
        if ($withPrices) {
            $values['prices'] = [];
        }

        foreach (VisibilityEnum::VISIBILITIES as $visibility) {
            $values['counters'][$visibility . 'Counters'] = ['children' => 0, $counterKey => 0];

            if ($withPrices) {
                $values['prices'][$visibility . 'Prices'] = [];
            }
        }

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
