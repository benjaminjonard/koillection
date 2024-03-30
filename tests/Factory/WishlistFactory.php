<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Wishlist;
use App\Enum\VisibilityEnum;
use App\Repository\WishlistRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Wishlist>
 *
 * @method static Wishlist|Proxy                     createOne(array $attributes = [])
 * @method static Wishlist[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Wishlist[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Wishlist|Proxy                     find(object|array|mixed $criteria)
 * @method static Wishlist|Proxy                     findOrCreate(array $attributes)
 * @method static Wishlist|Proxy                     first(string $sortedField = 'id')
 * @method static Wishlist|Proxy                     last(string $sortedField = 'id')
 * @method static Wishlist|Proxy                     random(array $attributes = [])
 * @method static Wishlist|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Wishlist[]|Proxy[]                 all()
 * @method static Wishlist[]|Proxy[]                 findBy(array $attributes)
 * @method static Wishlist[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Wishlist[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static WishlistRepository|RepositoryProxy repository()
 * @method        Wishlist|Proxy                     create(array|callable $attributes = [])
 */
final class WishlistFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->word(),
            'seenCounter' => self::faker()->randomNumber(),
            'cachedValues' => [],
            'visibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Wishlist::class;
    }
}
