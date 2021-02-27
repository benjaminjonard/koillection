<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

use App\Entity\User;

interface LoggableInterface
{
    public function getId() : ?string;

    public function getOwner() : ?User;

    public function __toString() : string;
}
