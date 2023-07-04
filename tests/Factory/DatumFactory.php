<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Datum;
use App\Enum\DatumTypeEnum;
use App\Repository\DatumRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Datum>
 *
 * @method static Datum|Proxy                     createOne(array $attributes = [])
 * @method static Datum[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Datum[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Datum|Proxy                     find(object|array|mixed $criteria)
 * @method static Datum|Proxy                     findOrCreate(array $attributes)
 * @method static Datum|Proxy                     first(string $sortedField = 'id')
 * @method static Datum|Proxy                     last(string $sortedField = 'id')
 * @method static Datum|Proxy                     random(array $attributes = [])
 * @method static Datum|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Datum[]|Proxy[]                 all()
 * @method static Datum[]|Proxy[]                 findBy(array $attributes)
 * @method static Datum[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Datum[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static DatumRepository|RepositoryProxy repository()
 * @method        Datum|Proxy                     create(array|callable $attributes = [])
 */
final class DatumFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'label' => self::faker()->word(),
            'type' => DatumTypeEnum::TYPE_TEXT,
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Datum::class;
    }
}
