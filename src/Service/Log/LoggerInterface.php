<?php

namespace App\Service\Log;

use App\Entity\Log;

/**
 * Interface LoggerInterface
 *
 * @package App\Service\Log
 */
interface LoggerInterface
{
    /**
     * @return string
     */
    public function getClass() : string;

    /**
     * @return string
     */
    public function getLabelGetter() : string;

    /**
     * @return int
     */
    public function getPriority() : int;

    /**
     * @param $entity
     * @return Log|null
     */
    public function getCreateLog($entity) : ?Log;

    /**
     * @param $entity
     * @return Log|null
     */
    public function getDeleteLog($entity) : ?Log;

    /**
     * @param $entity
     * @param array $changeset
     * @param array $relations
     * @return Log|null
     */
    public function getUpdateLog($entity, array $changeset, array $relations = []) : ?Log;

    /**
     * @param $entity
     * @param array $payload
     * @return null|string
     */
    public function formatPayload($entity, array $payload) : ?string;
}
