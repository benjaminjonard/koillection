<?php

namespace App\Service\Log;

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
     * @return null
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
     * @return null
     */
    public function getCreateLog($entity)
    {
        return $this->getLoggerResponse('getCreateLog', [$entity]);
    }

    /**
     * @param $entity
     * @return null
     */
    public function getDeleteLog($entity)
    {
        return $this->getLoggerResponse('getDeleteLog', [$entity]);
    }

    /**
     * @param $entity
     * @param $changeset
     * @param array $relations
     * @return null
     */
    public function getUpdateLog($entity, $changeset, array $relations = [])
    {
        return $this->getLoggerResponse('getUpdateLog', [$entity, $changeset, $relations]);
    }

    /**
     * @param $class
     * @param $payload
     * @return null
     */
    public function getFormattedPayload($class, $payload)
    {
        return $this->getLoggerResponse('formatPayload', [$class, $payload]);
    }
}
