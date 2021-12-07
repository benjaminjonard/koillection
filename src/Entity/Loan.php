<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LoanRepository")
 * @ORM\Table(name="koi_loan")
 */
class Loan
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="loans")
     */
    private ?Item $item;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $lentTo = null;

    /**
     * @ORM\Column(type="date")
     */
    private ?\DateTimeInterface $lentAt = null;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?\DateTimeInterface $returnedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private ?User $owner = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId() : ?string
    {
        return $this->id->toString();
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

    public function getLentAt(): ?\DateTimeInterface
    {
        return $this->lentAt;
    }

    public function setLentAt(\DateTimeInterface $lentAt): self
    {
        $this->lentAt = $lentAt;

        return $this;
    }

    public function getReturnedAt(): ?\DateTimeInterface
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(?\DateTimeInterface $returnedAt): self
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
