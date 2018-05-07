<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Connection
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ConnectionRepository")
 * @ORM\Table(name="koi_connection")
 */
class Connection
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)     *
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $userAgent;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="connections")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $browserName;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $browserVersion;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $engineName;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $engineVersion;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $osName;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $osVersion;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deviceType;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deviceSubtype;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deviceManufacturer;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deviceModel;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deviceIdentifier;

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
     * Set userAgent
     *
     * @param string $userAgent
     *
     * @return Connection
     */
    public function setUserAgent(string $userAgent) : Connection
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get userAgent
     *
     * @return string
     */
    public function getUserAgent() : string
    {
        return $this->userAgent;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Connection
     */
    public function setDate(\DateTime $date) : Connection
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate() : \DateTime
    {
        return $this->date;
    }

    /**
     * Set user
     *
     * @param \App\Entity\User $user
     *
     * @return Connection
     */
    public function setUser(User $user = null) : Connection
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User
     */
    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * @return null|string
     */
    public function getBrowserName(): ?string
    {
        return $this->browserName;
    }

    /**
     * @param null|string $browserName
     * @return Connection
     */
    public function setBrowserName(?string $browserName): Connection
    {
        $this->browserName = $browserName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBrowserVersion(): ?string
    {
        return $this->browserVersion;
    }

    /**
     * @param null|string $browserVersion
     * @return Connection
     */
    public function setBrowserVersion(?string $browserVersion): Connection
    {
        $this->browserVersion = $browserVersion;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEngineName(): ?string
    {
        return $this->engineName;
    }

    /**
     * @param null|string $engineName
     * @return Connection
     */
    public function setEngineName(?string $engineName): Connection
    {
        $this->engineName = $engineName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEngineVersion(): ?string
    {
        return $this->engineVersion;
    }

    /**
     * @param null|string $engineVersion
     * @return Connection
     */
    public function setEngineVersion(?string $engineVersion): Connection
    {
        $this->engineVersion = $engineVersion;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getOsName(): ?string
    {
        return $this->osName;
    }

    /**
     * @param null|string $osName
     * @return Connection
     */
    public function setOsName(?string $osName): Connection
    {
        $this->osName = $osName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getOsVersion(): ?string
    {
        return $this->osVersion;
    }

    /**
     * @param null|string $osVersion
     * @return Connection
     */
    public function setOsVersion(?string $osVersion): Connection
    {
        $this->osVersion = $osVersion;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDeviceType(): ?string
    {
        return $this->deviceType;
    }

    /**
     * @param null|string $deviceType
     * @return Connection
     */
    public function setDeviceType(?string $deviceType): Connection
    {
        $this->deviceType = $deviceType;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDeviceSubtype(): ?string
    {
        return $this->deviceSubtype;
    }

    /**
     * @param null|string $deviceSubtype
     * @return Connection
     */
    public function setDeviceSubtype(?string $deviceSubtype): Connection
    {
        $this->deviceSubtype = $deviceSubtype;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDeviceManufacturer(): ?string
    {
        return $this->deviceManufacturer;
    }

    /**
     * @param null|string $deviceManufacturer
     * @return Connection
     */
    public function setDeviceManufacturer(?string $deviceManufacturer): Connection
    {
        $this->deviceManufacturer = $deviceManufacturer;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDeviceModel(): ?string
    {
        return $this->deviceModel;
    }

    /**
     * @param null|string $deviceModel
     * @return Connection
     */
    public function setDeviceModel(?string $deviceModel): Connection
    {
        $this->deviceModel = $deviceModel;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDeviceIdentifier(): ?string
    {
        return $this->deviceIdentifier;
    }

    /**
     * @param null|string $deviceIdentifier
     * @return Connection
     */
    public function setDeviceIdentifier(?string $deviceIdentifier): Connection
    {
        $this->deviceIdentifier = $deviceIdentifier;

        return $this;
    }
}
