<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Loan
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\LoanRepository")
 * @ORM\Table(name="koi_loan")
 */
class Loan
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @var \App\Entity\Item
     * @ORM\ManyToOne(targetEntity="Item")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $item;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $lentTo;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    private $lentAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $returnedAt;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $owner;

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
     * Set lentTo.
     *
     * @param string $lentTo
     *
     * @return Loan
     */
    public function setLentTo(string $lentTo) : Loan
    {
        $this->lentTo = $lentTo;

        return $this;
    }

    /**
     * Get lentTo.
     *
     * @return string
     */
    public function getLentTo() : ?string
    {
        return $this->lentTo;
    }

    /**
     * Set lentAt.
     *
     * @param \DateTime $lentAt
     *
     * @return Loan
     */
    public function setLentAt(\DateTime $lentAt) : Loan
    {
        $this->lentAt = $lentAt;

        return $this;
    }

    /**
     * Get lentAt.
     *
     * @return \DateTime
     */
    public function getLentAt() : ?\DateTime
    {
        return $this->lentAt;
    }

    /**
     * Set returnedAt.
     *
     * @param \DateTime $returnedAt
     *
     * @return Loan
     */
    public function setReturnedAt(\DateTime $returnedAt) : Loan
    {
        $this->returnedAt = $returnedAt;

        return $this;
    }

    /**
     * Get returnedAt.
     *
     * @return \DateTime
     */
    public function getReturnedAt() : ?\DateTime
    {
        return $this->returnedAt;
    }

    /**
     * Set item.
     *
     * @param \App\Entity\Item $item
     *
     * @return Loan
     */
    public function setItem(Item $item = null) : Loan
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item.
     *
     * @return \App\Entity\Item
     */
    public function getItem() : Item
    {
        return $this->item;
    }

    /**
     * Set owner.
     *
     * @param \App\Entity\User $owner
     *
     * @return Loan
     */
    public function setOwner(User $owner = null) : Loan
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner.
     *
     * @return User|null
     */
    public function getOwner() : ?User
    {
        return $this->owner;
    }
}
