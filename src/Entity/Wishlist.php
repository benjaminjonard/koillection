<?php

declare(strict_types=1);

namespace App\Entity;

use App\Annotation\Upload;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\CacheableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\VisibilityEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WishlistRepository")
 * @ORM\Table(name="koi_wishlist", indexes={
 *     @ORM\Index(name="idx_wishlist_visibility", columns={"visibility"})
 * })
 */
class Wishlist implements BreadcrumbableInterface, CacheableInterface, LoggableInterface
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $id;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @var ?User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="wishlists")
     */
    private ?User $owner = null;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Wish", mappedBy="wishlist", cascade={"all"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private DoctrineCollection $wishes;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=6)
     */
    private ?string $color = null;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Wishlist", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private DoctrineCollection $children;

    /**
     * @var ?Wishlist
     * @ORM\ManyToOne(targetEntity="Wishlist", inversedBy="children")
     */
    private ?Wishlist $parent = null;

    /**
     * @var ?File
     * @Upload(path="image")
     */
    private ?File $file = null;

    /**
     * @var ?string
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $image = null;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private int $seenCounter;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $visibility;

    /**
     * @var ?\DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @var ?\DateTimeInterface
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->wishes = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
        $this->seenCounter = 0;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() ?? '';
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

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

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

    /**
     * @return DoctrineCollection|Wish[]
     */
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

    /**
     * @return DoctrineCollection|Wishlist[]
     */
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
        if ($file) {
            $this->setUpdatedAt(new \DateTime());
        }

        return $this;
    }
}
