<?php

namespace App\Entity\Interfaces;

use App\Entity\User;

/**
 * Interface BreadcrumbableInterface
 *
 * @package App\Entity\Interfaces
 */
interface BreadcrumbableInterface
{
    /**
     * @return string|null
     */
    public function getId() : ?string;

    /**
     * @return User|null
     */
    public function getOwner() : ?User;

    /**
     * @return null|string
     */
    public function __toString() : ?string;
}
