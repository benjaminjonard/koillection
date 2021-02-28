<?php

declare(strict_types=1);

namespace App\Doctrine;

final class QueryNameGenerator
{
    private int $incrementedAssociation = 1;

    private int $incrementedName = 1;

    public function generateJoinAlias(string $association): string
    {
        return sprintf('%s_a%d', $association, $this->incrementedAssociation++);
    }

    public function generateParameterName(string $name): string
    {
        return sprintf('%s_p%d', str_replace('.', '_', $name), $this->incrementedName++);
    }
}
