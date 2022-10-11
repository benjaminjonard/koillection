<?php

namespace App\Factory;

use App\Entity\Datum;
use App\Repository\DatumRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Datum>
 *
 * @method static Datum|Proxy createOne(array $attributes = [])
 * @method static Datum[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Datum[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Datum|Proxy find(object|array|mixed $criteria)
 * @method static Datum|Proxy findOrCreate(array $attributes)
 * @method static Datum|Proxy first(string $sortedField = 'id')
 * @method static Datum|Proxy last(string $sortedField = 'id')
 * @method static Datum|Proxy random(array $attributes = [])
 * @method static Datum|Proxy randomOrCreate(array $attributes = [])
 * @method static Datum[]|Proxy[] all()
 * @method static Datum[]|Proxy[] findBy(array $attributes)
 * @method static Datum[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Datum[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static DatumRepository|RepositoryProxy repository()
 * @method Datum|Proxy create(array|callable $attributes = [])
 */
final class DatumFactory extends ModelFactory
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
            'type' => self::faker()->text(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Datum $datum): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Datum::class;
    }
}
