<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Path;
use App\Enum\DatumTypeEnum;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Path>
 *
 * @method        Path|Proxy                       create(array|callable $attributes = [])
 * @method static Path|Proxy                       createOne(array $attributes = [])
 * @method static Path|Proxy                       find(object|array|mixed $criteria)
 * @method static Path|Proxy                       findOrCreate(array $attributes)
 * @method static Path|Proxy                       first(string $sortedField = 'id')
 * @method static Path|Proxy                       last(string $sortedField = 'id')
 * @method static Path|Proxy                       random(array $attributes = [])
 * @method static Path|Proxy                       randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Path[]|Proxy[]                   all()
 * @method static Path[]|Proxy[]                   createMany(int $number, array|callable $attributes = [])
 * @method static Path[]|Proxy[]                   createSequence(iterable|callable $sequence)
 * @method static Path[]|Proxy[]                   findBy(array $attributes)
 * @method static Path[]|Proxy[]                   randomRange(int $min, int $max, array $attributes = [])
 * @method static Path[]|Proxy[]                   randomSet(int $number, array $attributes = [])
 */
final class PathFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->text(),
            'path' => self::faker()->text(),
            'position' => self::faker()->randomNumber(),
            'type' => DatumTypeEnum::TYPE_TEXT,
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Path::class;
    }
}
