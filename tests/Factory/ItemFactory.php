<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Item;
use App\Enum\VisibilityEnum;
use App\Repository\ItemRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Item>
 *
 * @method static Item|Proxy                     createOne(array $attributes = [])
 * @method static Item[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Item[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Item|Proxy                     find(object|array|mixed $criteria)
 * @method static Item|Proxy                     findOrCreate(array $attributes)
 * @method static Item|Proxy                     first(string $sortedField = 'id')
 * @method static Item|Proxy                     last(string $sortedField = 'id')
 * @method static Item|Proxy                     random(array $attributes = [])
 * @method static Item|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Item[]|Proxy[]                 all()
 * @method static Item[]|Proxy[]                 findBy(array $attributes)
 * @method static Item[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Item[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ItemRepository|RepositoryProxy repository()
 * @method        Item|Proxy                     create(array|callable $attributes = [])
 */
final class ItemFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->word(),
            'quantity' => 1,
            'seenCounter' => self::faker()->randomNumber(),
            'visibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'parentVisibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'finalVisibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Item::class;
    }
}
