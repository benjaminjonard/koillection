<?php

declare(strict_types=1);

namespace App\Doctrine;

final class QueryNameGenerator
{
    /**
     * @var int
     */
    private int $incrementedAssociation = 1;

    /**
     * @var int
     */
    private int $incrementedName = 1;

    /**
     * @param string $association
     * @return string
     */
    public function generateJoinAlias(string $association): string
    {
        return sprintf('%s_a%d', $association, $this->incrementedAssociation++);
    }

    /**
     * @param string $name
     * @return string
     */
    public function generateParameterName(string $name): string
    {
        return sprintf('%s_p%d', str_replace('.', '_', $name), $this->incrementedName++);
    }
}
