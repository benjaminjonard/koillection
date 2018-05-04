<?php

namespace App\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class OwnershipFilter
 *
 * @package App\Doctrine\Filter
 */
class OwnershipFilter extends SQLFilter
{
    /**
     * @param ClassMetadata $targetEntity
     * @param string $targetTableAlias
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->getReflectionClass()->hasProperty('owner')) {
            return '';
        }

        return sprintf('%s.owner_id = %s', $targetTableAlias, $this->getParameter('id'));
    }
}
