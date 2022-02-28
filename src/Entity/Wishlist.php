<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Attribute\Upload;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\CacheableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\VisibilityEnum;
use App\Repository\WishlistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WishlistRepository::class)]
#[ORM\Table(name: "koi_wishlist")]
#[ORM\Index(name: "idx_wishlist_final_visibility", columns: ["final_visibility"])]
#[ApiResource(
    normalizationContext: ["groups" => ["wishlist:read"]],
    denormalizationContext: ["groups" => ["wishlist:write"]],
    collectionOperations: [
        "get",
        "post" => ["input_formats" => ["multipart" => ["multipart/form-data"]]],
    ]
)]
class Wishlist implements BreadcrumbableInterface, CacheableInterface, LoggableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36, unique: true, options: ["fixed" => true])]
    #[Groups(["wishlist:read"])]
    private string $id;

    #[ORM\Column(type: "string")]
    #[Groups(["wishlist:read", "wishlist:write"])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: "User", inversedBy: "wishlists")]
    #[Groups(["wishlist:read"])]
    private ?User $owner = null;

    #[ORM\OneToMany(targetEntity: "Wish", mappedBy: "wishlist", cascade: ["all"])]
    #[ORM\OrderBy(["name" => "ASC"])]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $wishes;

    #[ORM\Column(type: "string", length: 6)]
    #[Groups(["wishlist:read"])]
    private ?string $color = null;

    #[ORM\OneToMany(targetEntity: "Wishlist", mappedBy: "parent", cascade: ["all"])]
    #[ORM\OrderBy(["name" => "ASC"])]
    #[Groups(["wishlist:read"])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $children;

    #[ORM\ManyToOne(targetEntity: "Wishlist", inversedBy: "children")]
    #[Groups(["wishlist:read", "wishlist:write"])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ApiSubresource(maxDepth: 1)]
    #[Assert\Expression("not (value == this)", message: "error.parent.same_as_current_object")]
    private ?Wishlist $parent = null;

    #[Upload(path: "image", maxWidth: 200, maxHeight: 200)]
    #[Assert\Image(mimeTypes: ["image/png", "image/jpeg", "image/webp"])]
    #[Groups(["wishlist:write"])]
    private ?File $file = null;

    #[ORM\Column(type: "string", nullable: true, unique: true)]
    #[Groups(["wishlist:read"])]
    private ?string $image = null;

    #[ORM\Column(type: "integer")]
    #[Groups(["wishlist:read"])]
    private int $seenCounter;

    #[ORM\Column(type: "string", length: 10)]
    #[Groups(["wishlist:read", "wishlist:write"])]
    private string $visibility;

    #[ORM\Column(type: "string", length: 10, nullable: true)]
    #[Groups(["wishlist:read"])]
    private ?string $parentVisibility;

    #[ORM\Column(type: "string", length: 10)]
    #[Groups(["wishlist:read"])]
    private string $finalVisibility;

    #[ORM\Column(type: "datetime")]
    #[Groups(["wishlist:read"])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Groups(["wishlist:read"])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->wishes = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
        $this->seenCounter = 0;
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    public function getId(): ?string
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getSeenCounter(): ?int
    {
        return $this->seenCounter;
    }

    public function setSeenCounter(int $seenCounter): self
    {
        $this->seenCounter = $seenCounter;

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

    public function getWishes(): DoctrineCollection
    {
        return $this->wishes;
    }

    public function addWish(Wish $wish): self
    {
        if (!$this->wishes->contains($wish)) {
            $this->wishes[] = $wish;
            $wish->setWishlist($this);
        }

        return $this;
    }

    public function removeWish(Wish $wish): self
    {
        if ($this->wishes->contains($wish)) {
            $this->wishes->removeElement($wish);
            // set the owning side to null (unless already changed)
            if ($wish->getWishlist() === $this) {
                $wish->setWishlist(null);
            }
        }

        return $this;
    }

    public function getChildren(): DoctrineCollection
    {
        return $this->children;
    }

    public function addChild(Wishlist $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Wishlist $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;
        //Force Doctrine to trigger an update
        if ($file instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getParentVisibility(): ?string
    {
        return $this->parentVisibility;
    }

    public function setParentVisibility(?string $parentVisibility): self
    {
        $this->parentVisibility = $parentVisibility;

        return $this;
    }

    public function getFinalVisibility(): string
    {
        return $this->finalVisibility;
    }

    public function setFinalVisibility(string $finalVisibility): self
    {
        $this->finalVisibility = $finalVisibility;

        return $this;
    }
}
