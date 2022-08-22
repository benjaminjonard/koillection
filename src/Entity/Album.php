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
use App\Enum\DisplayModeEnum;
use App\Enum\VisibilityEnum;
use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
#[ORM\Table(name: 'koi_album')]
#[ORM\Index(name: 'idx_album_final_visibility', columns: ['final_visibility'])]
#[ApiResource(
    normalizationContext: ['groups' => ['album:read']],
    denormalizationContext: ['groups' => ['album:write']],
    collectionOperations: [
        'get',
        'post' => ['input_formats' => [
            'json' => ['application/json', 'application/ld+json'],
            'multipart' => ['multipart/form-data'],
        ]],
    ],
    itemOperations: [
        'get',
        'put',
        'delete',
        'patch',
        'image' => [
            'method' => 'POST',
            'path' => '/albums/{id}/image',
            'controller' => UploadController::class,
            'denormalization_context' => ['groups' => ['album:image']],
            'input_formats' => ['multipart' => ['multipart/form-data']],
            'openapi_context' => ['summary' => 'Upload the Album image.']
        ]
    ]
)]
class Album implements BreadcrumbableInterface, LoggableInterface, CacheableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['album:read'])]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Groups(['album:read', 'album:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::STRING, length: 6)]
    #[Groups(['album:read'])]
    private ?string $color = null;

    #[Upload(path: 'image', maxWidth: 200, maxHeight: 200)]
    #[Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/webp'])]
    #[Groups(['album:write', 'album:image'])]
    private ?File $file = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['album:read'])]
    private ?string $image = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'albums')]
    #[Groups(['album:read'])]
    private ?User $owner = null;

    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'album', cascade: ['all'])]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $photos;

    #[ORM\OneToMany(targetEntity: Album::class, mappedBy: 'parent', cascade: ['all'])]
    #[ORM\OrderBy(['title' => 'ASC'])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $children;

    #[ORM\ManyToOne(targetEntity: Album::class, inversedBy: 'children')]
    #[Groups(['album:read', 'album:write'])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ApiSubresource(maxDepth: 1)]
    #[Assert\Expression('not (value == this)', message: 'error.parent.same_as_current_object')]
    private ?Album $parent = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['album:read'])]
    private int $seenCounter;

    #[ORM\Column(type: Types::STRING, length: 4)]
    #[Groups(['tag:read', 'tag:write'])]
    #[Assert\Choice(choices: DisplayModeEnum::DISPLAY_MODES)]
    private string $photosDisplayMode;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['album:read', 'album:write'])]
    #[Assert\Choice(choices: VisibilityEnum::VISIBILITIES)]
    private string $visibility;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Groups(['album:read'])]
    private ?string $parentVisibility;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['album:read'])]
    private string $finalVisibility;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['album:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['album:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->seenCounter = 0;
        $this->photos = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
        $this->photosDisplayMode = DisplayModeEnum::DISPLAY_MODE_GRID;
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? '';
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getPhotos(): DoctrineCollection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setAlbum($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getAlbum() === $this) {
                $photo->setAlbum(null);
            }
        }

        return $this;
    }

    public function getChildren(): DoctrineCollection
    {
        return $this->children;
    }

    public function addChild(Album $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Album $child): self
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

    public function getPhotosDisplayMode(): string
    {
        return $this->photosDisplayMode;
    }

    public function setPhotosDisplayMode(string $photosDisplayMode): Album
    {
        $this->photosDisplayMode = $photosDisplayMode;

        return $this;
    }
}
