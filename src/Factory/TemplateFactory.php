<?php

namespace App\Factory;

use App\Entity\Template;
use App\Repository\TemplateRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Template>
 *
 * @method static Template|Proxy createOne(array $attributes = [])
 * @method static Template[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Template[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Template|Proxy find(object|array|mixed $criteria)
 * @method static Template|Proxy findOrCreate(array $attributes)
 * @method static Template|Proxy first(string $sortedField = 'id')
 * @method static Template|Proxy last(string $sortedField = 'id')
 * @method static Template|Proxy random(array $attributes = [])
 * @method static Template|Proxy randomOrCreate(array $attributes = [])
 * @method static Template[]|Proxy[] all()
 * @method static Template[]|Proxy[] findBy(array $attributes)
 * @method static Template[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Template[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TemplateRepository|RepositoryProxy repository()
 * @method Template|Proxy create(array|callable $attributes = [])
 */
final class TemplateFactory extends ModelFactory
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
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Template $template): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Template::class;
    }
}
