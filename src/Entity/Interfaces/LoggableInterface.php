<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

use App\Entity\User;

interface LoggableInterface
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
     * @return string
     */
    public function __toString() : string;
}
