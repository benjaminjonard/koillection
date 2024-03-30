<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Collection;
use App\Enum\VisibilityEnum;
use App\Repository\CollectionRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Collection>
 *
 * @method static Collection|Proxy                     createOne(array $attributes = [])
 * @method static Collection[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Collection[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Collection|Proxy                     find(object|array|mixed $criteria)
 * @method static Collection|Proxy                     findOrCreate(array $attributes)
 * @method static Collection|Proxy                     first(string $sortedField = 'id')
 * @method static Collection|Proxy                     last(string $sortedField = 'id')
 * @method static Collection|Proxy                     random(array $attributes = [])
 * @method static Collection|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Collection[]|Proxy[]                 all()
 * @method static Collection[]|Proxy[]                 findBy(array $attributes)
 * @method static Collection[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Collection[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static CollectionRepository|RepositoryProxy repository()
 * @method        Collection|Proxy                     create(array|callable $attributes = [])
 */
final class CollectionFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'parent' => null,
            'title' => self::faker()->word(),
            'seenCounter' => self::faker()->randomNumber(),
            'visibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(static function (Collection $collection): void {
                $collection->getChildrenDisplayConfiguration()->setOwner($collection->getOwner());
                $collection->getItemsDisplayConfiguration()->setOwner($collection->getOwner());
            })
        ;
    }

    protected static function getClass(): string
    {
        return Collection::class;
    }
}
