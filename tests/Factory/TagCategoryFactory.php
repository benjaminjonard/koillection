<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\TagCategory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class TagCategoryFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'label' => self::faker()->word(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public static function class(): string
    {
        return TagCategory::class;
    }
}
