<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use App\Entity\Datum;
use App\Entity\User;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class VisibilityFilter
 *
 * @package App\Doctrine\Filter
 */
class VisibilityFilter extends SQLFilter
{
    /**
     * @param ClassMetadata $targetEntity
     * @param string $targetTableAlias
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->getReflectionClass()->hasProperty('visibility')) {
            return '';
        }

        /*
         * If preview context, do not apply visibility filter on User entity as it will prevent the user to see
         * the preview if its visibility is private
         *
         * Quotes are added by doctrine, because it's supposed to be used in a query
         */
        if ($this->getParameter('context') === "'preview'" && $targetEntity->getName() === User::class) {
            return '';
        }

        return sprintf("%s.visibility = '%s'", $targetTableAlias, VisibilityEnum::VISIBILITY_PUBLIC);
    }
}
