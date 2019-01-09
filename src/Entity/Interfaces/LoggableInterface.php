<?php

namespace App\Entity\Interfaces;

use App\Entity\User;

/**
 * Interface LoggableInterface
 *
 * @package App\Entity\Interfaces
 */
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
