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
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->text(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Template::class;
    }
}
