<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\ChoiceList;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ChoiceListFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->word(),
            'choices' => [],
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public static function class(): string
    {
        return ChoiceList::class;
    }
}
