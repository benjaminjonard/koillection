<?php

declare(strict_types=1);

namespace App\Service\Log\Logger;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Service\Log\Logger;
use App\Service\Log\LogQueue;
use Symfony\Contracts\Translation\TranslatorInterface;

class DatumCollectionLogger extends Logger
{
    public function __construct(
        TranslatorInterface $translator,
        private LogQueue $logQueue
    ) {
        parent::__construct($translator);
    }

    public function getClass() : string
    {
        return Collection::class;
    }

    public function getPriority() : int
    {
        return 2;
    }

    public function supports($object) : bool
    {
        return get_class($object) === Datum::class && $object->getCollection() instanceof Collection;
    }

    public function getCreateLog(LoggableInterface $datum) : ?Log
    {
        if (!$this->supports($datum)) {
            return null;
        }

        //If the collection was just created, we log nothing more
        if ($this->logQueue->find($datum->getCollection()->getId(), Collection::class, LogTypeEnum::TYPE_CREATE)) {
            return null;
        }

        $log = $this->logQueue->find($datum->getCollection()->getId(), Collection::class, LogTypeEnum::TYPE_UPDATE);
        if (!$log) {
            $log = $this->createLog(LogTypeEnum::TYPE_UPDATE, $datum->getCollection());
        }
        $payload = json_decode($log->getPayload(), true);
        $payload[] = [
            'title' => $datum->getCollection()->getTitle(),
            'property' => 'datum_added',
            'datum_label' => $datum->getLabel(),
            'datum_value' => $datum->getValue(),
            'datum_type' => $datum->getType()
        ];
        $log->setPayload(json_encode($payload));

        return $log;
    }

    public function getDeleteLog(LoggableInterface $datum) : ?Log
    {
        if (!$this->supports($datum)) {
            return null;
        }

        $log = $this->logQueue->find($datum->getCollection()->getId(), Collection::class, LogTypeEnum::TYPE_UPDATE);
        if (!$log) {
            $log = $this->createLog(LogTypeEnum::TYPE_UPDATE, $datum->getCollection());
        }
        $payload = json_decode($log->getPayload(), true);
        $payload[] = [
            'title' => $datum->getCollection()->getTitle(),
            'property' => 'datum_removed',
            'datum_label' => $datum->getLabel(),
            'datum_value' => $datum->getValue(),
            'datum_type' => $datum->getType()
        ];
        $log->setPayload(json_encode($payload));

        return $log;
    }

    public function getUpdateLog(LoggableInterface $datum, array $changeset, array $relations = []) : ?Log
    {
        return null;
    }

    public function formatPayload(string $class, array $payload) : ?string
    {
        return null;
    }
}
