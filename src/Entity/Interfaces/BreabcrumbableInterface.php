<?php

namespace App\Entity\Interfaces;

use App\Entity\User;

/**
 * Interface BreabcrumbableInterface
 *
 * @package App\Entity\Interfaces
 */
interface BreabcrumbableInterface
{
    /**
     * Get an ordered array representing the breadcrumb for an entity.
     *
     * @param $context
     * @return mixed
     */
    public function getBreadcrumb($context);
}
