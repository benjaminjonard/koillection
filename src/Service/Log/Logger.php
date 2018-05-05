<?php

namespace App\Service\Log;

use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class Logger
 *
 * @package App\Service\Log
 */
abstract class Logger implements LoggerInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param $class
     * @return bool
     */
    public function supportedClass($class) : bool
    {
        return $class === $this->getClass();
    }

    /**
     * @param $type
     * @param $entity
     * @param array $payload
     * @return Log
     */
    public function createLog($type, LoggableInterface $entity, array $payload = []) : Log
    {
        $log = new Log();
        $log
            ->setType($type)
            ->setObjectId($entity->getId())
            ->setObjectLabel($entity->__toString())
            ->setObjectClass($this->getClass())
            ->setUsername($entity->getOwner()->getUsername())
            ->setPayload(json_encode($payload))
        ;

        return $log;
    }
}
