<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\TagCategory;
use App\Repository\TagCategoryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<TagCategory>
 *
 * @method static TagCategory|Proxy                     createOne(array $attributes = [])
 * @method static TagCategory[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static TagCategory[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static TagCategory|Proxy                     find(object|array|mixed $criteria)
 * @method static TagCategory|Proxy                     findOrCreate(array $attributes)
 * @method static TagCategory|Proxy                     first(string $sortedField = 'id')
 * @method static TagCategory|Proxy                     last(string $sortedField = 'id')
 * @method static TagCategory|Proxy                     random(array $attributes = [])
 * @method static TagCategory|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TagCategory[]|Proxy[]                 all()
 * @method static TagCategory[]|Proxy[]                 findBy(array $attributes)
 * @method static TagCategory[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static TagCategory[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static TagCategoryRepository|RepositoryProxy repository()
 * @method        TagCategory|Proxy                     create(array|callable $attributes = [])
 */
final class TagCategoryFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'label' => self::faker()->word(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return TagCategory::class;
    }
}
