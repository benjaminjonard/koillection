<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Configuration;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ConfigurationFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public static function class(): string
    {
        return Configuration::class;
    }
}
