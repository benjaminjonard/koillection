<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Interfaces\BreadcrumbableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: "koi_inventory")]
#[ApiResource(
    normalizationContext: ["groups" => ["inventory:read"]],
    denormalizationContext: ["groups" => ["inventory:write"]],
)]
class Inventory implements BreadcrumbableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36, unique: true, options: ["fixed" => true])]
    #[Groups(["inventory:read"])]
    private string $id;

    #[ORM\Column(type: "string")]
    #[Groups(["inventory:read", "inventory:write"])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: "json")]
    #[Groups(["inventory:read", "inventory:write"])]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: "User", inversedBy: "inventories")]
    #[Groups(["inventory:read"])]
    private ?User $owner = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(["inventory:read"])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Groups(["inventory:read"])]
    private ?\DateTimeInterface $updatedAt;

    private array $contentAsArray = [];

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    public function getContentAsArray() : array
    {
        if (!empty($this->contentAsArray)) {
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

    public function getId() : ?string
    {
        return $this->id;
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
