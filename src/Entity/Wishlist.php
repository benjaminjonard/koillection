<?php

declare(strict_types=1);

namespace App\Entity;

use Api\Controller\UploadController;
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
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WishlistRepository::class)]
#[ORM\Table(name: 'koi_wishlist')]
#[ORM\Index(name: 'idx_wishlist_final_visibility', columns: ['final_visibility'])]
#[ApiResource(
    normalizationContext: ['groups' => ['wishlist:read']],
    denormalizationContext: ['groups' => ['wishlist:write']],
    collectionOperations: [
        'get',
        'post' => ['input_formats' => [
            'json' => ['application/json', 'application/ld+json'],
            'multipart' => ['multipart/form-data']
        ]],
    ],
    itemOperations: [
        'get',
        'put',
        'delete',
        'patch',
        'image' => [
            'method' => 'POST',
            'path' => '/wishlists/{id}/image',
            'controller' => UploadController::class,
            'denormalization_context' => ['groups' => ['wishlist:image']],
            'input_formats' => ['multipart' => ['multipart/form-data']],
            'openapi_context' => ['summary' => 'Upload the Wishlist image.']
        ]
    ]
)]
class Wishlist implements BreadcrumbableInterface, CacheableInterface, LoggableInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['wishlist:read'])]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['wishlist:read', 'wishlist:write'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'wishlists')]
    #[Groups(['wishlist:read'])]
    private ?User $owner = null;

    #[ORM\OneToMany(targetEntity: Wish::class, mappedBy: 'wishlist', cascade: ['all'])]
    #[ORM\OrderBy(['name' => Criteria::ASC])]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $wishes;

    #[ORM\Column(type: Types::STRING, length: 6)]
    #[Groups(['wishlist:read'])]
    private ?string $color = null;

    #[ORM\OneToMany(targetEntity: Wishlist::class, mappedBy: 'parent', cascade: ['all'])]
    #[ORM\OrderBy(['name' => Criteria::ASC])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $children;

    #[ORM\ManyToOne(targetEntity: Wishlist::class, inversedBy: 'children')]
    #[Groups(['wishlist:read', 'wishlist:write'])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ApiSubresource(maxDepth: 1)]
    #[Assert\Expression('not (value == this)', message: 'error.parent.same_as_current_object')]
    private ?Wishlist $parent = null;

    #[Upload(path: 'image', maxWidth: 200, maxHeight: 200)]
    #[Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/webp'])]
    #[Groups(['wishlist:write', 'wishlist:image'])]
    private ?File $file = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['wishlist:read'])]
    private ?string $image = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['wishlist:read'])]
    private int $seenCounter = 0;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['wishlist:read', 'wishlist:write'])]
    #[Assert\Choice(choices: VisibilityEnum::VISIBILITIES)]
    private string $visibility = VisibilityEnum::VISIBILITY_PUBLIC;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Groups(['wishlist:read'])]
    private ?string $parentVisibility = null;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['wishlist:read'])]
    private string $finalVisibility;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['wishlist:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['wishlist:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->wishes = new ArrayCollection();
        $this->children = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
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
        // Force Doctrine to trigger an update
        if ($file instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTimeImmutable());
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
