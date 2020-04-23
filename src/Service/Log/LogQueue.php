<?php

declare(strict_types=1);

namespace App\Service\Log;

use App\Entity\Log;

/**
 * Class LogQueue
 *
 * @package App\Service\Log
 */
class LogQueue
{
    /**
     * @var array
     */
    private $logs = [];

    /**
     * @var bool
     */
    private $processQueue;

    public function __construct()
    {
        $this->processQueue = true;
    }

    /**
     * @param Log|null $log
     */
    public function addLog(?Log $log)
    {
        if (null === $log) {
            return;
        }

        //If there is already a log for the current Id/Class/Type, unset it
        foreach ($this->logs as $key => $existingLog) {
            if ($log->getObjectId() === $existingLog->getId() &&
                $log->getObjectClass() === $existingLog->getObjectClass() &&
                $log->getType() === $existingLog->getType()) {
                unset($this->logs[$key]);
                break;
            }
        }

        $this->logs[] = $log;
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param $id
     * @param $class
     * @param $type
     * @return mixed|null
     */
    public function find($id, $class, $type)
    {
        foreach ($this->logs as $log) {
            if ($log->getObjectId() === $id && $log->getObjectClass() === $class && $log->getType() === $type) {
                return $log;
            }
        }

        return null;
    }

    public function clearLogs()
    {
        $this->logs = [];
    }

    public function isQueueProcessable()
    {
        return $this->processQueue;
    }

    public function enableQueue()
    {
        $this->processQueue = true;
    }

    public function disableQueue()
    {
        $this->processQueue = false;
    }
}
