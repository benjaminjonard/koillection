<?php

namespace App\Factory;

use App\Entity\Wishlist;
use App\Repository\WishlistRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Wishlist>
 *
 * @method static Wishlist|Proxy createOne(array $attributes = [])
 * @method static Wishlist[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Wishlist[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Wishlist|Proxy find(object|array|mixed $criteria)
 * @method static Wishlist|Proxy findOrCreate(array $attributes)
 * @method static Wishlist|Proxy first(string $sortedField = 'id')
 * @method static Wishlist|Proxy last(string $sortedField = 'id')
 * @method static Wishlist|Proxy random(array $attributes = [])
 * @method static Wishlist|Proxy randomOrCreate(array $attributes = [])
 * @method static Wishlist[]|Proxy[] all()
 * @method static Wishlist[]|Proxy[] findBy(array $attributes)
 * @method static Wishlist[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Wishlist[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static WishlistRepository|RepositoryProxy repository()
 * @method Wishlist|Proxy create(array|callable $attributes = [])
 */
final class WishlistFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'name' => self::faker()->text(),
            'color' => self::faker()->text(),
            'seenCounter' => self::faker()->randomNumber(),
            'cachedValues' => [],
            'visibility' => self::faker()->text(),
            'finalVisibility' => self::faker()->text(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Wishlist $wishlist): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Wishlist::class;
    }
}
