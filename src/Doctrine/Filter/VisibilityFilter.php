<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use App\Enum\VisibilityEnum;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class VisibilityFilter extends SQLFilter
{
    public function getVisibilities(): array
    {
        if ("''" === $this->getParameter('user')) {
            return [VisibilityEnum::VISIBILITY_PUBLIC];
        }

        return [VisibilityEnum::VISIBILITY_PUBLIC, VisibilityEnum::VISIBILITY_INTERNAL];
    }

    #[\Override]
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ($targetEntity->getReflectionClass()->hasProperty('finalVisibility')) {
            return $this->buildFilter($targetTableAlias, 'final_visibility');
        }

        if ($targetEntity->getReflectionClass()->hasProperty('visibility')) {
            return $this->buildFilter($targetTableAlias, 'visibility');
        }

        return '';
    }

    private function buildFilter(string $targetTableAlias, string $property): string
    {
        // If user not authenticated, only show public
        if ("''" === $this->getParameter('user')) {
            return sprintf("%s.%s = '%s'", $targetTableAlias, $property, VisibilityEnum::VISIBILITY_PUBLIC);
        }

        // If authenticated, show public and internal
        return sprintf(
            "%s.%s IN ('%s', '%s')",
            $targetTableAlias,
            $property,
            VisibilityEnum::VISIBILITY_PUBLIC,
            VisibilityEnum::VISIBILITY_INTERNAL
        );
    }
}
