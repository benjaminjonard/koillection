<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Log
 *
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="koi_log")
 */
class Log
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $type;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $loggedAt;

    /**
     * @var integer
     * @ORM\Column(type="uuid")
     */
    private $objectId;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $objectLabel;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $objectClass;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $payload;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    /**
     * Set loggedAt
     *
     * @param \DateTime $loggedAt
     *
     * @return Log
     */
    public function setLoggedAt($loggedAt)
    {
        $this->loggedAt = $loggedAt;

        return $this;
    }

    /**
     * Get loggedAt
     *
     * @return \DateTime
     */
    public function getLoggedAt()
    {
        return $this->loggedAt;
    }

    /**
     * @param $objectId
     * @return Log
     */
    public function setObjectId($objectId) : Log
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set objectClass
     *
     * @param string $objectClass
     *
     * @return Log
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    /**
     * Get objectClass
     *
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * Set payload
     *
     * @param string $payload
     *
     * @return Log
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Get payload
     *
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Log
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Log
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set objectLabel
     *
     * @param string $objectLabel
     *
     * @return Log
     */
    public function setObjectLabel($objectLabel)
    {
        $this->objectLabel = $objectLabel;

        return $this;
    }

    /**
     * Get objectLabel
     *
     * @return string
     */
    public function getObjectLabel()
    {
        return $this->objectLabel;
    }
}
