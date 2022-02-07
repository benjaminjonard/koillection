<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Repository\TagCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TagCategoryRepository::class)]
#[ORM\Table(name: "koi_tag_category")]
#[ApiResource(
    normalizationContext: ["groups" => ["tagCategory:read"]],
    denormalizationContext: ["groups" => ["tagCategory:write"]],
)]
class TagCategory implements BreadcrumbableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36, unique: true, options: ["fixed" => true])]
    #[Groups(["tagCategory:read"])]
    private string $id;

    #[ORM\Column(type: "string")]
    #[Groups(["tagCategory:read", "tagCategory:write"])]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["tagCategory:read", "tagCategory:write"])]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 7)]
    #[Groups(["tagCategory:read", "tagCategory:write"])]
    private ?string $color = null;

    #[ORM\ManyToOne(targetEntity: "User", inversedBy: "tagCategories")]
    #[Groups(["tagCategory:read"])]
    private ?User $owner = null;

    #[ORM\OneToMany(targetEntity: "Tag", mappedBy: "category")]
    #[Groups(["tagCategory:read"])]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $tags;

    #[ORM\Column(type: "datetime")]
    #[Groups(["tagCategory:read"])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Groups(["tagCategory:read"])]
    private ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->tags = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

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

    public function getTags(): DoctrineCollection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setCategory($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            // set the owning side to null (unless already changed)
            if ($tag->getCategory() === $this) {
                $tag->setCategory(null);
            }
        }

        return $this;
    }
}
