<?php

declare(strict_types=1);

namespace App\Service\Log;

use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class Logger implements LoggerInterface
{
    public function __construct(
        protected TranslatorInterface $translator
    ) {
    }

    public function supportsClass(string $class): bool
    {
        return $class === $this->getClass();
    }

    public function supports(LoggableInterface $object): bool
    {
        return \get_class($object) === $this->getClass();
    }

    public function createLog(string $type, LoggableInterface $entity, array $payload = []): Log
    {
        $log = new Log();
        $log
            ->setType($type)
            ->setObjectId($entity->getId())
            ->setObjectLabel($entity->__toString())
            ->setObjectClass($this->getClass())
            ->setOwner($entity->getOwner())
            ->setPayload(json_encode($payload))
        ;

        return $log;
    }
}
