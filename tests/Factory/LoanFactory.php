<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Loan;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class LoanFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'lentTo' => self::faker()->firstName(),
            'lentAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public static function class(): string
    {
        return Loan::class;
    }
}
