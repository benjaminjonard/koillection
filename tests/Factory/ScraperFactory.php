<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Scraper;
use App\Repository\ScraperRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Scraper>
 *
 * @method        Scraper|Proxy                     create(array|callable $attributes = [])
 * @method static Scraper|Proxy                     createOne(array $attributes = [])
 * @method static Scraper|Proxy                     find(object|array|mixed $criteria)
 * @method static Scraper|Proxy                     findOrCreate(array $attributes)
 * @method static Scraper|Proxy                     first(string $sortedField = 'id')
 * @method static Scraper|Proxy                     last(string $sortedField = 'id')
 * @method static Scraper|Proxy                     random(array $attributes = [])
 * @method static Scraper|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ScraperRepository|RepositoryProxy repository()
 * @method static Scraper[]|Proxy[]                 all()
 * @method static Scraper[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Scraper[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Scraper[]|Proxy[]                 findBy(array $attributes)
 * @method static Scraper[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Scraper[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ScraperFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'name' => self::faker()->text(),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Scraper::class;
    }
}
