<?php

namespace App\Factory;

use App\Entity\Loan;
use App\Repository\LoanRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Loan>
 *
 * @method static Loan|Proxy createOne(array $attributes = [])
 * @method static Loan[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Loan[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Loan|Proxy find(object|array|mixed $criteria)
 * @method static Loan|Proxy findOrCreate(array $attributes)
 * @method static Loan|Proxy first(string $sortedField = 'id')
 * @method static Loan|Proxy last(string $sortedField = 'id')
 * @method static Loan|Proxy random(array $attributes = [])
 * @method static Loan|Proxy randomOrCreate(array $attributes = [])
 * @method static Loan[]|Proxy[] all()
 * @method static Loan[]|Proxy[] findBy(array $attributes)
 * @method static Loan[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Loan[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static LoanRepository|RepositoryProxy repository()
 * @method Loan|Proxy create(array|callable $attributes = [])
 */
final class LoanFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'lentTo' => self::faker()->firstName(),
            'lentAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Loan::class;
    }
}
