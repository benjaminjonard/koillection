<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")
 * @ORM\Table(name="koi_log")
 */
class Log
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private string $type;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $loggedAt = null;

    /**
     * @var string
     * @ORM\Column(type="uuid")
     */
    private string $objectId;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $objectLabel;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $objectClass;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private bool $objectDeleted;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private string $payload;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="logs")
     */
    private User $owner;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->objectDeleted = false;
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLoggedAt(): ?\DateTimeInterface
    {
        return $this->loggedAt;
    }

    public function setLoggedAt(\DateTimeInterface $loggedAt): self
    {
        $this->loggedAt = $loggedAt;

        return $this;
    }

    public function getObjectId()
    {
        return $this->objectId;
    }

    public function setObjectId(string $objectId): self
    {
        $this->objectId = $objectId;

        return $this;
    }

    public function getObjectLabel(): ?string
    {
        return $this->objectLabel;
    }

    public function setObjectLabel(string $objectLabel): self
    {
        $this->objectLabel = $objectLabel;

        return $this;
    }

    public function getObjectClass(): ?string
    {
        return $this->objectClass;
    }

    public function setObjectClass(string $objectClass): self
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    public function isObjectDeleted(): ?bool
    {
        return $this->objectDeleted;
    }

    public function setObjectDeleted(bool $objectDeleted): self
    {
        $this->objectDeleted = $objectDeleted;

        return $this;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(?string $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
