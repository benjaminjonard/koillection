<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attribute\Upload;
use App\Entity\Interfaces\CacheableInterface;
use App\Entity\Traits\VisibilityTrait;
use App\Enum\VisibilityEnum;
use App\Repository\WishRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WishRepository::class)]
#[ORM\Table(name: "koi_wish")]
#[ORM\Index(name: "idx_wish_final_visibility", columns: ["final_visibility"])]
class Wish implements CacheableInterface
{
    use VisibilityTrait;

    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36, unique: true, options: ["fixed" => true])]
    private string $id;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $price = null;

    #[ORM\Column(type: "string", length: 6, nullable: true)]
    private ?string $currency;

    #[ORM\ManyToOne(targetEntity: "Wishlist", inversedBy: "wishes")]
    private ?Wishlist $wishlist;

    #[ORM\ManyToOne(targetEntity: "User")]
    private ?User $owner = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $comment = null;

    #[Upload(path: "image", smallThumbnailPath: "imageSmallThumbnail")]
    private ?File $file = null;

    #[ORM\Column(type: "string", nullable: true, unique: true)]
    private ?string $image = null;

    #[ORM\Column(type: "string", nullable: true, unique: true)]
    private ?string $imageSmallThumbnail = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageSmallThumbnail(): ?string
    {
        if ($this->imageSmallThumbnail === null) {
            return $this->image;
        }

        return $this->imageSmallThumbnail;
    }

    public function setImageSmallThumbnail(?string $imageSmallThumbnail): self
    {
        $this->imageSmallThumbnail = $imageSmallThumbnail;

        return $this;
    }
}
