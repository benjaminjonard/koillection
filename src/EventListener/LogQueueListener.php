<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Service\Log\LogQueue;
use Doctrine\Persistence\ManagerRegistry;

class LogQueueListener
{
    private LogQueue $logQueue;

    private ManagerRegistry $managerRegistry;

    public function __construct(LogQueue $logQueue, ManagerRegistry $managerRegistry)
    {
        $this->logQueue = $logQueue;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     *   As we are on a kernel response event, this code is triggered at every response.
     *   We have then to check if the EntityManager is still opened because if there was a problem
     *   related to Doctrine before this event, the em may have been closed and another error will come up,
     *   hiding the original one (bad for logging the true error).
     */
    public function onKernelResponse()
    {
        if ($this->logQueue->isQueueProcessable() && !empty($this->logQueue->getLogs()) && $this->managerRegistry->getManager()->isOpen()) {
            $deletedIds = [];
            foreach ($this->logQueue->getLogs() as $log) {
                if ($log->getType() === LogTypeEnum::TYPE_DELETE) {
                    $deletedIds[] = $log->getObjectId();
                }
            }

            //Persist logs
            foreach ($this->logQueue->getLogs() as $log) {
                if (
                    $log->getType() === LogTypeEnum::TYPE_DELETE ||
                    ($log->getType() !== LogTypeEnum::TYPE_DELETE && !\in_array($log->getObjectId(), $deletedIds))) {
                    $this->managerRegistry->getManager()->persist($log);
                }
            }

            //If we have some 'delete' logs, set property objectDeleted to true on all logs concerning this object
            if (!empty($deletedIds)) {
                $this->managerRegistry->getManager()->createQueryBuilder()
                    ->update(Log::class, 'l')
                    ->set('l.objectDeleted', '?1')
                    ->where('l.objectId IN (?2)')
                    ->setParameter(1, true)
                    ->setParameter(2, $deletedIds)
                    ->getQuery()
                    ->execute()
                ;
            }

            $this->managerRegistry->getManager()->flush();
        }

        $this->logQueue->clearLogs();
    }
}
