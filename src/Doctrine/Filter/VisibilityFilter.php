<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use App\Entity\Datum;
use App\Entity\User;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class VisibilityFilter extends SQLFilter
{

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (!$targetEntity->getReflectionClass()->hasProperty('visibility')) {
            return '';
        }

        if ($this->getParameter('user') === "''") {
            return sprintf("%s.visibility = '%s'", $targetTableAlias, VisibilityEnum::VISIBILITY_PUBLIC);
        }

        return sprintf("%s.visibility IN ('%s', '%s')",
            $targetTableAlias,
            VisibilityEnum::VISIBILITY_PUBLIC,
            VisibilityEnum::VISIBILITY_AUTHENTICATED_USERS_ONLY
        );
    }
}
