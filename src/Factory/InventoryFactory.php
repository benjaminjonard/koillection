<?php

namespace App\Factory;

use App\Entity\Inventory;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Inventory>
 *
 * @method static Inventory|Proxy createOne(array $attributes = [])
 * @method static Inventory[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Inventory[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Inventory|Proxy find(object|array|mixed $criteria)
 * @method static Inventory|Proxy findOrCreate(array $attributes)
 * @method static Inventory|Proxy first(string $sortedField = 'id')
 * @method static Inventory|Proxy last(string $sortedField = 'id')
 * @method static Inventory|Proxy random(array $attributes = [])
 * @method static Inventory|Proxy randomOrCreate(array $attributes = [])
 * @method static Inventory[]|Proxy[] all()
 * @method static Inventory[]|Proxy[] findBy(array $attributes)
 * @method static Inventory[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Inventory[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method Inventory|Proxy create(array|callable $attributes = [])
 */
final class InventoryFactory extends ModelFactory
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
            'content' => [],
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Inventory $inventory): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Inventory::class;
    }
}
