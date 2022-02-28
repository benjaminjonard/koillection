<?php

declare(strict_types=1);

namespace App\Service\Log\Logger;

use App\Entity\Datum;
use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Item;
use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Service\Log\Logger;
use App\Service\Log\LogQueue;
use Symfony\Contracts\Translation\TranslatorInterface;

class DatumItemLogger extends Logger
{
    public function __construct(
        TranslatorInterface $translator,
        private LogQueue $logQueue
    ) {
        parent::__construct($translator);
    }

    public function getClass(): string
    {
        return Item::class;
    }

    public function getPriority(): int
    {
        return 2;
    }

    public function supports($object): bool
    {
        return \get_class($object) === Datum::class && $object->getItem() instanceof Item;
    }

    public function getCreateLog(LoggableInterface $datum): ?Log
    {
        if (!$this->supports($datum)) {
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

    public function getDeleteLog(LoggableInterface $datum): ?Log
    {
        if (!$this->supports($datum)) {
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

    public function getUpdateLog(LoggableInterface $datum, array $changeset, array $relations = []): ?Log
    {
        return null;
    }

    public function formatPayload(string $class, array $payload): ?string
    {
        return null;
    }
}
