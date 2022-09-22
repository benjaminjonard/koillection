<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AlbumRepository;
use App\Repository\CollectionRepository;
use App\Repository\WishlistRepository;
use App\Service\CachedValuesCalculator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:regenerate-cached-values',
    description: 'Regenerate cached values such has counters and prices',
)]
class RegeneratedCachedValuesCommand extends Command
{
    public function __construct(
        private readonly CachedValuesCalculator $cachedValuesCalculator,
        private readonly ManagerRegistry $managerRegistry,
        private readonly CollectionRepository $collectionRepository,
        private readonly WishlistRepository $wishlistRepository,
        private readonly AlbumRepository $albumRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootCollections = $this->collectionRepository->findBy(['parent' => null]);
        foreach ($rootCollections as $rootCollection) {
            $this->cachedValuesCalculator->computeForCollection($rootCollection);
        }

        $rootWishlists = $this->wishlistRepository->findBy(['parent' => null]);
        foreach ($rootWishlists as $rootWishlist) {
            $this->cachedValuesCalculator->computeForWishlist($rootWishlist);
        }

        $rootAlbums = $this->albumRepository->findBy(['parent' => null]);
        foreach ($rootAlbums as $rootAlbum) {
            $this->cachedValuesCalculator->computeForAlbum($rootAlbum);
        }

        $this->managerRegistry->getManager()->flush();

        return Command::SUCCESS;
    }
}
