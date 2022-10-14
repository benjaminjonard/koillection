<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ChoiceList;
use App\Repository\ChoiceListRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ChoiceList>
 *
 * @method static ChoiceList|Proxy                     createOne(array $attributes = [])
 * @method static ChoiceList[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static ChoiceList[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static ChoiceList|Proxy                     find(object|array|mixed $criteria)
 * @method static ChoiceList|Proxy                     findOrCreate(array $attributes)
 * @method static ChoiceList|Proxy                     first(string $sortedField = 'id')
 * @method static ChoiceList|Proxy                     last(string $sortedField = 'id')
 * @method static ChoiceList|Proxy                     random(array $attributes = [])
 * @method static ChoiceList|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ChoiceList[]|Proxy[]                 all()
 * @method static ChoiceList[]|Proxy[]                 findBy(array $attributes)
 * @method static ChoiceList[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static ChoiceList[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ChoiceListRepository|RepositoryProxy repository()
 * @method        ChoiceList|Proxy                     create(array|callable $attributes = [])
 */
final class ChoiceListFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->word(),
            'choices' => [],
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return ChoiceList::class;
    }
}
