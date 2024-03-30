<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Wish;
use App\Enum\VisibilityEnum;
use App\Repository\WishRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Wish>
 *
 * @method static Wish|Proxy                     createOne(array $attributes = [])
 * @method static Wish[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Wish[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Wish|Proxy                     find(object|array|mixed $criteria)
 * @method static Wish|Proxy                     findOrCreate(array $attributes)
 * @method static Wish|Proxy                     first(string $sortedField = 'id')
 * @method static Wish|Proxy                     last(string $sortedField = 'id')
 * @method static Wish|Proxy                     random(array $attributes = [])
 * @method static Wish|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Wish[]|Proxy[]                 all()
 * @method static Wish[]|Proxy[]                 findBy(array $attributes)
 * @method static Wish[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Wish[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static WishRepository|RepositoryProxy repository()
 * @method        Wish|Proxy                     create(array|callable $attributes = [])
 */
final class WishFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->word(),
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
        return Wish::class;
    }
}
