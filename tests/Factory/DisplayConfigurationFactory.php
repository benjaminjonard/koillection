<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\DisplayConfiguration;
use App\Repository\DisplayConfigurationRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<DisplayConfiguration>
 *
 * @method static DisplayConfiguration|Proxy                     createOne(array $attributes = [])
 * @method static DisplayConfiguration[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static DisplayConfiguration[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static DisplayConfiguration|Proxy                     find(object|array|mixed $criteria)
 * @method static DisplayConfiguration|Proxy                     findOrCreate(array $attributes)
 * @method static DisplayConfiguration|Proxy                     first(string $sortedField = 'id')
 * @method static DisplayConfiguration|Proxy                     last(string $sortedField = 'id')
 * @method static DisplayConfiguration|Proxy                     random(array $attributes = [])
 * @method static DisplayConfiguration|Proxy                     randomOrCreate(array $attributes = [])
 * @method static DisplayConfiguration[]|Proxy[]                 all()
 * @method static DisplayConfiguration[]|Proxy[]                 findBy(array $attributes)
 * @method static DisplayConfiguration[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static DisplayConfiguration[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static DisplayConfigurationRepository|RepositoryProxy repository()
 * @method        DisplayConfiguration|Proxy                     create(array|callable $attributes = [])
 */
final class DisplayConfigurationFactory extends ModelFactory
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
            'displayMode' => self::faker()->text(),
            'sortingDirection' => self::faker()->text(),
            'showVisibility' => self::faker()->boolean(),
            'showActions' => self::faker()->boolean(),
            'showNumberOfChildren' => self::faker()->boolean(),
            'showNumberOfItems' => self::faker()->boolean(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(DisplayConfiguration $displayConfiguration): void {})
        ;
    }

    protected static function getClass(): string
    {
        return DisplayConfiguration::class;
    }
}
