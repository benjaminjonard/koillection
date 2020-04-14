<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ImageTypeEnum;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Wish
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\WishRepository")
 * @ORM\Table(name="koi_wish", indexes={
 *     @ORM\Index(name="idx_wish_visibility", columns={"visibility"})
 * })
 */
class Wish
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
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $url = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $price = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private string $currency;

    /**
     * @var Wishlist
     * @ORM\ManyToOne(targetEntity="Wishlist", inversedBy="wishes")
     */
    private Wishlist $wishlist;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private ?User $owner = null;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $comment = null;

    /**
     * @var Image
     * @ORM\OneToOne(targetEntity="Image", cascade={"all"}, orphanRemoval=true)
     */
    private ?Image $image = null;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $visibility;

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
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getWishlist(): ?Wishlist
    {
        return $this->wishlist;
    }

    public function setWishlist(?Wishlist $wishlist): self
    {
        $this->wishlist = $wishlist;

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

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $image->setType(ImageTypeEnum::TYPE_COMMON);
        $this->image = $image;

        return $this;
    }
}
