<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Repository\LoanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
#[ORM\Table(name: 'koi_loan')]
#[ApiResource(
    denormalizationContext: ['groups' => ['loan:write']],
    normalizationContext: ['groups' => ['loan:read']]
)]
#[ApiResource(uriTemplate: '/items/{id}/loans', uriVariables: ['id' => new Link(fromClass: Item::class, fromProperty: 'loans')], normalizationContext: ['groups' => ['loan:read']], operations: [new GetCollection()])]
class Loan
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['loan:read'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'loans')]
    #[Groups(['loan:read', 'loan:write'])]
    #[Assert\NotBlank]
    private ?Item $item = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Groups(['loan:read', 'loan:write'])]
    private ?string $lentTo = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['loan:read', 'loan:write'])]
    private ?\DateTimeImmutable $lentAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['loan:read', 'loan:write'])]
    private ?\DateTimeImmutable $returnedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['loan:read'])]
    private ?User $owner = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLentTo(): ?string
    {
        return $this->lentTo;
    }

    public function setLentTo(string $lentTo): self
    {
        $this->lentTo = $lentTo;

        return $this;
    }

    public function getLentAt(): ?\DateTimeImmutable
    {
        return $this->lentAt;
    }

    public function setLentAt(\DateTimeImmutable $lentAt): self
    {
        $this->lentAt = $lentAt;

        return $this;
    }

    public function getReturnedAt(): ?\DateTimeImmutable
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(?\DateTimeImmutable $returnedAt): self
    {
        $this->returnedAt = $returnedAt;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

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
