<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Scraper;
use App\Enum\ScraperContentTypeEnum;
use App\Enum\ScraperTypeEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends \Zenstruck\Foundry\Persistence\PersistentObjectFactory<\App\Entity\Scraper>
 */
final class ScraperFactory extends \Zenstruck\Foundry\Persistence\PersistentObjectFactory
{
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'name' => self::faker()->text(),
            'type' => ScraperTypeEnum::TYPE_ITEM,
            'contentType' => ScraperContentTypeEnum::CONTENT_TYPE_HTML,
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }

    #[\Override]
    public static function class(): string
    {
        return Scraper::class;
    }
}
