<?php

namespace App\Factory;

use App\Entity\Wish;
use App\Repository\WishRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Wish>
 *
 * @method static Wish|Proxy createOne(array $attributes = [])
 * @method static Wish[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Wish[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Wish|Proxy find(object|array|mixed $criteria)
 * @method static Wish|Proxy findOrCreate(array $attributes)
 * @method static Wish|Proxy first(string $sortedField = 'id')
 * @method static Wish|Proxy last(string $sortedField = 'id')
 * @method static Wish|Proxy random(array $attributes = [])
 * @method static Wish|Proxy randomOrCreate(array $attributes = [])
 * @method static Wish[]|Proxy[] all()
 * @method static Wish[]|Proxy[] findBy(array $attributes)
 * @method static Wish[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Wish[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static WishRepository|RepositoryProxy repository()
 * @method Wish|Proxy create(array|callable $attributes = [])
 */
final class WishFactory extends ModelFactory
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
            'visibility' => self::faker()->text(),
            'finalVisibility' => self::faker()->text(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Wish $wish): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Wish::class;
    }
}
