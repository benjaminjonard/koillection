<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Log;
use App\Repository\LogRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Log>
 *
 * @method static Log|Proxy                     createOne(array $attributes = [])
 * @method static Log[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Log[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Log|Proxy                     find(object|array|mixed $criteria)
 * @method static Log|Proxy                     findOrCreate(array $attributes)
 * @method static Log|Proxy                     first(string $sortedField = 'id')
 * @method static Log|Proxy                     last(string $sortedField = 'id')
 * @method static Log|Proxy                     random(array $attributes = [])
 * @method static Log|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Log[]|Proxy[]                 all()
 * @method static Log[]|Proxy[]                 findBy(array $attributes)
 * @method static Log[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Log[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static LogRepository|RepositoryProxy repository()
 * @method        Log|Proxy                     create(array|callable $attributes = [])
 */
final class LogFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'loggedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'objectId' => self::faker()->uuid(),
            'objectLabel' => self::faker()->word(),
            'objectClass' => self::faker()->word(),
            'objectDeleted' => self::faker()->boolean(),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Log::class;
    }
}
