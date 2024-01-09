<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Configuration;
use App\Repository\DisplayConfigurationRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Configuration>
 *
 * @method static Configuration|Proxy                            createOne(array $attributes = [])
 * @method static Configuration[]|Proxy[]                        createMany(int $number, array|callable $attributes = [])
 * @method static Configuration[]|Proxy[]                        createSequence(array|callable $sequence)
 * @method static Configuration|Proxy                            find(object|array|mixed $criteria)
 * @method static Configuration|Proxy                            findOrCreate(array $attributes)
 * @method static Configuration|Proxy                            first(string $sortedField = 'id')
 * @method static Configuration|Proxy                            last(string $sortedField = 'id')
 * @method static Configuration|Proxy                            random(array $attributes = [])
 * @method static Configuration|Proxy                            randomOrCreate(array $attributes = [])
 * @method static Configuration[]|Proxy[]                        all()
 * @method static Configuration[]|Proxy[]                        findBy(array $attributes)
 * @method static Configuration[]|Proxy[]                        randomSet(int $number, array $attributes = [])
 * @method static Configuration[]|Proxy[]                        randomRange(int $min, int $max, array $attributes = [])
 * @method static DisplayConfigurationRepository|RepositoryProxy repository()
 * @method        Configuration|Proxy                            create(array|callable $attributes = [])
 */
final class ConfigurationFactory extends ModelFactory
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
        return Configuration::class;
    }
}
