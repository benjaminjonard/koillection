<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Photo;
use App\Enum\VisibilityEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class PhotoFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->text(),
            'visibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public static function class(): string
    {
        return Photo::class;
    }
}
