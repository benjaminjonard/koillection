<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Field;
use App\Enum\DatumTypeEnum;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Field>
 *
 * @method static Field|Proxy     createOne(array $attributes = [])
 * @method static Field[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Field[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Field|Proxy     find(object|array|mixed $criteria)
 * @method static Field|Proxy     findOrCreate(array $attributes)
 * @method static Field|Proxy     first(string $sortedField = 'id')
 * @method static Field|Proxy     last(string $sortedField = 'id')
 * @method static Field|Proxy     random(array $attributes = [])
 * @method static Field|Proxy     randomOrCreate(array $attributes = [])
 * @method static Field[]|Proxy[] all()
 * @method static Field[]|Proxy[] findBy(array $attributes)
 * @method static Field[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Field[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method        Field|Proxy     create(array|callable $attributes = [])
 */
final class FieldFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->text(),
            'type' => DatumTypeEnum::TYPE_TEXT,
            'position' => self::faker()->randomNumber(),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Field::class;
    }
}
