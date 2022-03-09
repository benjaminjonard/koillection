<?php

declare(strict_types=1);

namespace App\Service\Log;

use App\Entity\Log;

class LogQueue
{
    private array $logs = [];

    private bool $processQueue;

    public function __construct()
    {
        $this->processQueue = true;
    }

    public function addLog(?Log $log): void
    {
        if (null === $log) {
            return;
        }

        // If there is already a log for the current Id/Class/Type, unset it
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

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function find($id, string $class, string $type): ?Log
    {
        foreach ($this->logs as $log) {
            if ($log->getObjectId() === $id && $log->getObjectClass() === $class && $log->getType() === $type) {
                return $log;
            }
        }

        return null;
    }

    public function clearLogs(): void
    {
        $this->logs = [];
    }

    public function isQueueProcessable(): bool
    {
        return $this->processQueue;
    }

    public function enableQueue(): void
    {
        $this->processQueue = true;
    }

    public function disableQueue(): void
    {
        $this->processQueue = false;
    }
}
