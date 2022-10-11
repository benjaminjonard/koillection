<?php

namespace App\Factory;

use App\Entity\TagCategory;
use App\Repository\TagCategoryRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<TagCategory>
 *
 * @method static TagCategory|Proxy createOne(array $attributes = [])
 * @method static TagCategory[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static TagCategory[]|Proxy[] createSequence(array|callable $sequence)
 * @method static TagCategory|Proxy find(object|array|mixed $criteria)
 * @method static TagCategory|Proxy findOrCreate(array $attributes)
 * @method static TagCategory|Proxy first(string $sortedField = 'id')
 * @method static TagCategory|Proxy last(string $sortedField = 'id')
 * @method static TagCategory|Proxy random(array $attributes = [])
 * @method static TagCategory|Proxy randomOrCreate(array $attributes = [])
 * @method static TagCategory[]|Proxy[] all()
 * @method static TagCategory[]|Proxy[] findBy(array $attributes)
 * @method static TagCategory[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static TagCategory[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TagCategoryRepository|RepositoryProxy repository()
 * @method TagCategory|Proxy create(array|callable $attributes = [])
 */
final class TagCategoryFactory extends ModelFactory
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
            'label' => self::faker()->text(),
            'color' => self::faker()->text(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(TagCategory $tagCategory): void {})
        ;
    }

    protected static function getClass(): string
    {
        return TagCategory::class;
    }
}
