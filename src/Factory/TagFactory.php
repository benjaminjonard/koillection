<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Tag;
use App\Enum\VisibilityEnum;
use App\Repository\TagRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Tag>
 *
 * @method static Tag|Proxy                     createOne(array $attributes = [])
 * @method static Tag[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Tag[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Tag|Proxy                     find(object|array|mixed $criteria)
 * @method static Tag|Proxy                     findOrCreate(array $attributes)
 * @method static Tag|Proxy                     first(string $sortedField = 'id')
 * @method static Tag|Proxy                     last(string $sortedField = 'id')
 * @method static Tag|Proxy                     random(array $attributes = [])
 * @method static Tag|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Tag[]|Proxy[]                 all()
 * @method static Tag[]|Proxy[]                 findBy(array $attributes)
 * @method static Tag[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Tag[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static TagRepository|RepositoryProxy repository()
 * @method        Tag|Proxy                     create(array|callable $attributes = [])
 */
final class TagFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'label' => self::faker()->word(),
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
        return Tag::class;
    }
}
