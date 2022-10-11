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
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'lentTo' => self::faker()->text(),
            'lentAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Loan $loan): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Loan::class;
    }
}
