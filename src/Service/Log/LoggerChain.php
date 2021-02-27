<?php

declare(strict_types=1);

namespace App\Service\Log;

use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;

class LoggerChain
{
    private iterable $loggers;

    public function __construct(iterable $loggers)
    {
        $this->loggers = $loggers;
    }
    private function getLoggerResponse($function, array $params)
    {
        $response = null;
        $loggers = [];
        foreach ($this->loggers as $logger) {
            $loggers[] = $logger;
        }

        usort($loggers, function (LoggerInterface $a, LoggerInterface $b) {
            return ($a->getPriority() <=> $b->getPriority());
        });

        foreach ($loggers as $logger) {
            $response = $logger->$function(...$params);
            if ($response !== null) {
                break;
            }
        }

        return $response;
    }

    public function getCreateLog(LoggableInterface $entity) : ?Log
    {
        return $this->getLoggerResponse('getCreateLog', [$entity]);
    }

    public function getDeleteLog(LoggableInterface $entity) : ?Log
    {
        return $this->getLoggerResponse('getDeleteLog', [$entity]);
    }

    public function getUpdateLog(LoggableInterface $entity, array $changeset, array $relations = []) : ?Log
    {
        return $this->getLoggerResponse('getUpdateLog', [$entity, $changeset, $relations]);
    }

    public function getFormattedPayload(string $class, array $payload) : ?string
    {
        return $this->getLoggerResponse('formatPayload', [$class, $payload]);
    }
}
