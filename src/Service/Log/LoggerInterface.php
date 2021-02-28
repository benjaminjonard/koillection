<?php

declare(strict_types=1);

namespace App\Service\Log;

use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;

interface LoggerInterface
{
    public function getClass() : string;

    public function getPriority() : int;

    public function getCreateLog(LoggableInterface $entity) : ?Log;

    public function getDeleteLog(LoggableInterface $entity) : ?Log;

    public function getUpdateLog(LoggableInterface $entity, array $changeset, array $relations = []) : ?Log;

    public function formatPayload(string $class, array $payload) : ?string;
}
