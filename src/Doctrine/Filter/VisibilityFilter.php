<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use App\Enum\VisibilityEnum;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class VisibilityFilter extends SQLFilter
{

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        $filter = '';

        if ($targetEntity->getReflectionClass()->hasProperty('finalVisibility')) {
            $filter = $this->addFilter($targetTableAlias, 'final_visibility');
        } elseif ($targetEntity->getReflectionClass()->hasProperty('visibility')) {
            $filter = $this->addFilter($targetTableAlias, 'visibility');
        }

        return $filter;
    }

    private function addFilter($targetTableAlias, $property): string
    {
        // If user not authenticated, only show public
        if ($this->getParameter('user') === "''") {
            return sprintf("%s.%s = '%s'", $targetTableAlias, $property, VisibilityEnum::VISIBILITY_PUBLIC);
        }

        // If authenticated, show public and internal
        return sprintf("%s.%s IN ('%s', '%s')",
            $targetTableAlias,
            $property,
            VisibilityEnum::VISIBILITY_PUBLIC,
            VisibilityEnum::VISIBILITY_INTERNAL
        );
    }
}
