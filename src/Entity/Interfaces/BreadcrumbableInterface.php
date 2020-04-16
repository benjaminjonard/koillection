<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

use App\Entity\User;

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
