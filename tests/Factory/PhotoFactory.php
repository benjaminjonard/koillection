<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Photo;
use App\Enum\VisibilityEnum;
use App\Repository\PhotoRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Photo>
 *
 * @method static Photo|Proxy                     createOne(array $attributes = [])
 * @method static Photo[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Photo[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Photo|Proxy                     find(object|array|mixed $criteria)
 * @method static Photo|Proxy                     findOrCreate(array $attributes)
 * @method static Photo|Proxy                     first(string $sortedField = 'id')
 * @method static Photo|Proxy                     last(string $sortedField = 'id')
 * @method static Photo|Proxy                     random(array $attributes = [])
 * @method static Photo|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Photo[]|Proxy[]                 all()
 * @method static Photo[]|Proxy[]                 findBy(array $attributes)
 * @method static Photo[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Photo[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static PhotoRepository|RepositoryProxy repository()
 * @method        Photo|Proxy                     create(array|callable $attributes = [])
 */
final class PhotoFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->text(),
            'visibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'finalVisibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Photo::class;
    }
}
