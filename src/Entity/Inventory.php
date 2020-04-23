<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\BreadcrumbableInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="koi_inventory")
 */
class Inventory implements BreadcrumbableInterface
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
     * @ORM\Column(type="string")
     */
    private ?string $name = null;

    /**
     * @var string
     * @ORM\Column(type="json")
     */
    private ?string $content = null;

    /**
     * @var array
     */
    private array $contentAsArray = [];

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="inventories")
     */
    private ?User $owner = null;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime", nullable=true)
     */
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    public function getContentAsArray() : array
    {
        if ($this->contentAsArray) {
            return $this->contentAsArray;
        }

        $this->contentAsArray = json_decode($this->content, true);

        return $this->contentAsArray;
    }

    public function getCheckedItemsCount() : int
    {
        $content = $this->getContentAsArray();
        $checkedItems = 0;

        foreach ($content as $rootCollection) {
            $checkedItems += $rootCollection['totalCheckedItems'];
        }

        return $checkedItems;
    }

    public function getTotalItemsCount() : int
    {
        $content = $this->getContentAsArray();
        $totalItems = 0;

        foreach ($content as $rootCollection) {
            $totalItems += $rootCollection['totalItems'];
        }

        return $totalItems;
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
