<?php

namespace App\Service\Log\Logger;

use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Service\Log\Logger;
use App\Service\Log\LogQueue;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DatumLogger
 *
 * @package App\Service\Log\Logger
 */
class DatumLogger extends Logger
{
    /**
     * @var LogQueue
     */
    private $logQueue;

    /**
     * DatumLogger constructor.
     * @param TranslatorInterface $translator
     * @param LogQueue $logQueue
     */
    public function __construct(TranslatorInterface $translator, LogQueue $logQueue)
    {
        parent::__construct($translator);
        $this->logQueue = $logQueue;
    }

    /**
     * @return string
     */
    public function getClass() : string
    {
        return Item::class;
    }

    /**
     * @return int
     */
    public function getPriority() : int
    {
        return 2;
    }

    /**
     * @param $class
     * @return bool
     */
    public function supportedClass($class) : bool
    {
        return $class === Datum::class;
    }

    /**
     * @param $datum
     * @return Log|null
     */
    public function getCreateLog($datum) : ?Log
    {
        if (!$this->supportedClass(\get_class($datum))) {
            return null;
        }

        //If the item was just created, we log nothing more
        if ($this->logQueue->find($datum->getItem()->getId(), Item::class, LogTypeEnum::TYPE_CREATE)) {
            return null;
        }

        $log = $this->logQueue->find($datum->getItem()->getId(), Item::class, LogTypeEnum::TYPE_UPDATE);
        if (!$log) {
            $log = $this->createLog(LogTypeEnum::TYPE_UPDATE, $datum->getItem());
        }
        $payload = json_decode($log->getPayload(), true);
        $payload[] = [
            'title' => $datum->getItem()->getName(),
            'property' => 'datum_added',
            'datum_label' => $datum->getLabel(),
            'datum_value' => $datum->getValue(),
            'datum_type' => $datum->getType()
        ];
        $log->setPayload(json_encode($payload));

        return $log;
    }

    /**
     * @param $datum
     * @return Log|null
     */
    public function getDeleteLog($datum) : ?Log
    {
        if (!$this->supportedClass(\get_class($datum))) {
            return null;
        }

        //If the item was just deleted, we log nothing more
        if ($this->logQueue->find($datum->getItem()->getId(), Item::class, LogTypeEnum::TYPE_DELETE)) {
            return null;
        }

        $log = $this->logQueue->find($datum->getItem()->getId(), Item::class, LogTypeEnum::TYPE_UPDATE);
        if (!$log) {
            $log = $this->createLog(LogTypeEnum::TYPE_UPDATE, $datum->getItem());
        }
        $payload = json_decode($log->getPayload(), true);
        $payload[] = [
            'title' => $datum->getItem()->getName(),
            'property' => 'datum_removed',
            'datum_label' => $datum->getLabel(),
            'datum_value' => $datum->getValue(),
            'datum_type' => $datum->getType()
        ];
        $log->setPayload(json_encode($payload));

        return $log;
    }

    /**
     * @param $datum
     * @param array $changeset
     * @param array $relations
     * @return Log|null
     */
    public function getUpdateLog($datum, array $changeset, array $relations = []) : ?Log
    {
        return null;
    }

    /**
     * @param $class
     * @param array $payload
     * @return null|string
     */
    public function formatPayload($class, array $payload) : ?string
    {
        return null;
    }
}
