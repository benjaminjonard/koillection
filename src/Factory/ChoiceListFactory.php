<?php

namespace App\Factory;

use App\Entity\ChoiceList;
use App\Repository\ChoiceListRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ChoiceList>
 *
 * @method static ChoiceList|Proxy createOne(array $attributes = [])
 * @method static ChoiceList[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ChoiceList[]|Proxy[] createSequence(array|callable $sequence)
 * @method static ChoiceList|Proxy find(object|array|mixed $criteria)
 * @method static ChoiceList|Proxy findOrCreate(array $attributes)
 * @method static ChoiceList|Proxy first(string $sortedField = 'id')
 * @method static ChoiceList|Proxy last(string $sortedField = 'id')
 * @method static ChoiceList|Proxy random(array $attributes = [])
 * @method static ChoiceList|Proxy randomOrCreate(array $attributes = [])
 * @method static ChoiceList[]|Proxy[] all()
 * @method static ChoiceList[]|Proxy[] findBy(array $attributes)
 * @method static ChoiceList[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ChoiceList[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ChoiceListRepository|RepositoryProxy repository()
 * @method ChoiceList|Proxy create(array|callable $attributes = [])
 */
final class ChoiceListFactory extends ModelFactory
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
            'choices' => [],
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(ChoiceList $choiceList): void {})
        ;
    }

    protected static function getClass(): string
    {
        return ChoiceList::class;
    }
}
