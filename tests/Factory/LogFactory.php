<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Log;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class LogFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'loggedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'objectId' => self::faker()->uuid(),
            'objectLabel' => self::faker()->word(),
            'objectClass' => self::faker()->word(),
            'objectDeleted' => self::faker()->boolean(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public static function class(): string
    {
        return Log::class;
    }
}
