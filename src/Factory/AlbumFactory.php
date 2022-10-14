<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Album;
use App\Enum\VisibilityEnum;
use App\Repository\AlbumRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Album>
 *
 * @method static Album|Proxy                     createOne(array $attributes = [])
 * @method static Album[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Album[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Album|Proxy                     find(object|array|mixed $criteria)
 * @method static Album|Proxy                     findOrCreate(array $attributes)
 * @method static Album|Proxy                     first(string $sortedField = 'id')
 * @method static Album|Proxy                     last(string $sortedField = 'id')
 * @method static Album|Proxy                     random(array $attributes = [])
 * @method static Album|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Album[]|Proxy[]                 all()
 * @method static Album[]|Proxy[]                 findBy(array $attributes)
 * @method static Album[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Album[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static AlbumRepository|RepositoryProxy repository()
 * @method        Album|Proxy                     create(array|callable $attributes = [])
 */
final class AlbumFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->word(),
            'seenCounter' => self::faker()->randomNumber(),
            'visibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Album::class;
    }
}
