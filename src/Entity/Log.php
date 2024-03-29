<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\LogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Table(name: 'koi_log')]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection()
    ],
    denormalizationContext: ['groups' => ['log:write']],
    normalizationContext: ['groups' => ['log:read']]
)]
class Log
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['log:read'])]
    private string $id;

    #[ORM\Column(type: Types::STRING, length: 6, nullable: true)]
    #[Groups(['log:read'])]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['log:read'])]
    private ?\DateTimeImmutable $loggedAt = null;

    #[ORM\Column(type: Types::STRING, length: 36)]
    #[Groups(['log:read'])]
    private string $objectId;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['log:read'])]
    private string $objectLabel;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['log:read'])]
    private string $objectClass;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Groups(['log:read'])]
    private bool $objectDeleted = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'logs')]
    #[Groups(['log:read'])]
    private ?User $owner = null;

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getLoggedAt(): ?\DateTimeImmutable
    {
        return $this->loggedAt;
    }

    public function setLoggedAt(\DateTimeImmutable $loggedAt): self
    {
        $this->loggedAt = $loggedAt;

        return $this;
    }

    public function getObjectId(): string
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
