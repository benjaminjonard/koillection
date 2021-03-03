<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class OwnershipFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (!$targetEntity->getReflectionClass()->hasProperty('owner')) {
            return '';
        }

        return sprintf('%s.owner_id = %s', $targetTableAlias, $this->getParameter('id'));
    }
}
