<?php

namespace App\Factory;

use App\Entity\Log;
use App\Repository\LogRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Log>
 *
 * @method static Log|Proxy createOne(array $attributes = [])
 * @method static Log[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Log[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Log|Proxy find(object|array|mixed $criteria)
 * @method static Log|Proxy findOrCreate(array $attributes)
 * @method static Log|Proxy first(string $sortedField = 'id')
 * @method static Log|Proxy last(string $sortedField = 'id')
 * @method static Log|Proxy random(array $attributes = [])
 * @method static Log|Proxy randomOrCreate(array $attributes = [])
 * @method static Log[]|Proxy[] all()
 * @method static Log[]|Proxy[] findBy(array $attributes)
 * @method static Log[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Log[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static LogRepository|RepositoryProxy repository()
 * @method Log|Proxy create(array|callable $attributes = [])
 */
final class LogFactory extends ModelFactory
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
            'loggedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'objectId' => self::faker()->text(),
            'objectLabel' => self::faker()->text(),
            'objectClass' => self::faker()->text(),
            'objectDeleted' => self::faker()->boolean(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Log $log): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Log::class;
    }
}
