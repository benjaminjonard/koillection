<?php

namespace App\Service\Log;
use App\Entity\Log;

/**
 * Class LoggerChain
 *
 * @package App\Service\Log
 */
class LoggerChain
{
    /**
     * @var iterable
     */
    private $loggers;

    /**
     * LoggerChain constructor.
     * @param iterable $loggers
     */
    public function __construct(iterable $loggers)
    {
        $this->loggers = $loggers;
    }

    /**
     * @param $function
     * @param array $params
     * @return Log|string|null
     */
    private function getLoggerResponse($function, array $params)
    {
        $response = null;
        $loggers= [];
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

    /**
     * @param $entity
     * @return Log|null
     */
    public function getCreateLog($entity) : ?Log
    {
        return $this->getLoggerResponse('getCreateLog', [$entity]);
    }

    /**
     * @param $entity
     * @return Log|null
     */
    public function getDeleteLog($entity) : ?Log
    {
        return $this->getLoggerResponse('getDeleteLog', [$entity]);
    }

    /**
     * @param $entity
     * @param $changeset
     * @param array $relations
     * @return Log|null
     */
    public function getUpdateLog($entity, $changeset, array $relations = []) : ?Log
    {
        return $this->getLoggerResponse('getUpdateLog', [$entity, $changeset, $relations]);
    }

    /**
     * @param $class
     * @param $payload
     * @return null|string
     */
    public function getFormattedPayload($class, $payload) : ?string
    {
        return $this->getLoggerResponse('formatPayload', [$class, $payload]);
    }
}
