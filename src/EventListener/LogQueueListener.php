<?php

namespace App\EventListener;

use App\Service\Log\LogQueue;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class LogQueueListener
 *
 * @package App\EventListener
 */
class LogQueueListener
{
    /**
     * @var LogQueue
     */
    private $logQueue;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * LogQueueListener constructor.
     * @param LogQueue $logQueue
     * @param EntityManagerInterface $em
     */
    public function __construct(LogQueue $logQueue, EntityManagerInterface $em)
    {
        $this->logQueue = $logQueue;
        $this->em = $em;
    }

    /**
     *   As we are on a kernel response event, this code is triggered at every response.
     *   We have then to check if $em is still opened because if there was a problem
     *   related to Doctrine before this event, the em may have been closed and another error will come up,
     *   hiding the original one (bad for logging the true error).
     */
    public function onKernelResponse()
    {
        if ($this->logQueue->isQueueProcessable() && !empty($this->logQueue->getLogs()) && $this->em->isOpen()) {
            foreach ($this->logQueue->getLogs() as $log) {
                $this->em->persist($log);
            }

            $this->em->flush();
        }

        $this->logQueue->clearLogs();
        $this->em->clear();
    }
}
