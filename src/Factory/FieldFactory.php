<?php

namespace App\Factory;

use App\Entity\Field;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Field>
 *
 * @method static Field|Proxy createOne(array $attributes = [])
 * @method static Field[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Field[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Field|Proxy find(object|array|mixed $criteria)
 * @method static Field|Proxy findOrCreate(array $attributes)
 * @method static Field|Proxy first(string $sortedField = 'id')
 * @method static Field|Proxy last(string $sortedField = 'id')
 * @method static Field|Proxy random(array $attributes = [])
 * @method static Field|Proxy randomOrCreate(array $attributes = [])
 * @method static Field[]|Proxy[] all()
 * @method static Field[]|Proxy[] findBy(array $attributes)
 * @method static Field[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Field[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method Field|Proxy create(array|callable $attributes = [])
 */
final class FieldFactory extends ModelFactory
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
            'name' => self::faker()->text(),
            'position' => self::faker()->randomNumber(),
            'type' => self::faker()->text(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Field $field): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Field::class;
    }
}
