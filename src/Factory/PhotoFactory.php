<?php

namespace App\Factory;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Photo>
 *
 * @method static Photo|Proxy createOne(array $attributes = [])
 * @method static Photo[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Photo[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Photo|Proxy find(object|array|mixed $criteria)
 * @method static Photo|Proxy findOrCreate(array $attributes)
 * @method static Photo|Proxy first(string $sortedField = 'id')
 * @method static Photo|Proxy last(string $sortedField = 'id')
 * @method static Photo|Proxy random(array $attributes = [])
 * @method static Photo|Proxy randomOrCreate(array $attributes = [])
 * @method static Photo[]|Proxy[] all()
 * @method static Photo[]|Proxy[] findBy(array $attributes)
 * @method static Photo[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Photo[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PhotoRepository|RepositoryProxy repository()
 * @method Photo|Proxy create(array|callable $attributes = [])
 */
final class PhotoFactory extends ModelFactory
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
            'title' => self::faker()->text(),
            'visibility' => self::faker()->text(),
            'finalVisibility' => self::faker()->text(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Photo $photo): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Photo::class;
    }
}
