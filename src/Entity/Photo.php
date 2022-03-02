<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Attribute\Upload;
use App\Entity\Interfaces\CacheableInterface;
use App\Enum\VisibilityEnum;
use App\Repository\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[ORM\Table(name: 'koi_photo')]
#[ORM\Index(name: 'idx_photo_final_visibility', columns: ['final_visibility'])]
#[ApiResource(
    normalizationContext: ['groups' => ['photo:read']],
    denormalizationContext: ['groups' => ['photo:write']],
    collectionOperations: [
        'get',
        'post' => ['input_formats' => ['multipart' => ['multipart/form-data']]],
    ]
)]
class Photo implements CacheableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['photo:read'])]
    private string $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $comment = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $place = null;

    #[ORM\ManyToOne(targetEntity: 'Album', inversedBy: 'photos')]
    #[Assert\NotBlank]
    #[Groups(['photo:read', 'photo:write'])]
    #[ApiSubresource(maxDepth: 1)]
    private ?Album $album;

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[Groups(['photo:read'])]
    private ?User $owner = null;

    #[Upload(path: 'image', smallThumbnailPath: 'imageSmallThumbnail')]
    #[Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/gif'])]
    #[Groups(['photo:write'])]
    private ?File $file = null;

    #[ORM\Column(type: 'string', nullable: true, unique: true)]
    #[Groups(['photo:read'])]
    private ?string $image = null;

    #[ORM\Column(type: 'string', nullable: true, unique: true)]
    #[Groups(['photo:read'])]
    private ?string $imageSmallThumbnail = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['photo:read'])]
    private ?\DateTimeInterface $takenAt = null;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups(['photo:read', 'photo:write'])]
    private string $visibility;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    #[Groups(['photo:read'])]
    private ?string $parentVisibility;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups(['photo:read'])]
    private string $finalVisibility;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['photo:read'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['photo:read'])]
    private ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getTakenAt(): ?\DateTimeInterface
    {
        return $this->takenAt;
    }

    public function setTakenAt(?\DateTimeInterface $takenAt): self
    {
        $this->takenAt = $takenAt;

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

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

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
        if (null === $this->imageSmallThumbnail) {
            return $this->image;
        }

        return $this->imageSmallThumbnail;
    }

    public function setImageSmallThumbnail(?string $imageSmallThumbnail): self
    {
        $this->imageSmallThumbnail = $imageSmallThumbnail;

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
