<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\MetadataStorage;

final readonly class PlatformMetadataStorageFactory
{
    public function __invoke(DependencyFactory $dependencyFactory): MetadataStorage
    {
        return new PlatformMetadataStorage(
            $dependencyFactory->getMetadataStorage(),
            new PlatformMigrationsNamespace($dependencyFactory->getConnection())
        );
    }
}
