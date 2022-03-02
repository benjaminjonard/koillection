<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Attribute\Upload;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\VisibilityEnum;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'koi_tag')]
#[ORM\Index(name: 'idx_tag_visibility', columns: ['visibility'])]
#[ApiResource(
    normalizationContext: ['groups' => ['tag:read']],
    denormalizationContext: ['groups' => ['tag:write']],
    collectionOperations: [
        'get',
        'post' => ['input_formats' => ['multipart' => ['multipart/form-data']]],
    ]
)]
class Tag implements BreadcrumbableInterface, LoggableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['tag:read'])]
    private string $id;

    #[ORM\Column(type: 'string')]
    #[Groups(['tag:read', 'tag:write'])]
    #[Assert\NotBlank]
    private string $label;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['tag:read', 'tag:write'])]
    private ?string $description = null;

    #[Upload(path: 'image', smallThumbnailPath: 'imageSmallThumbnail')]
    #[Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/gif'])]
    #[Groups(['tag:write'])]
    private ?File $file = null;

    #[ORM\Column(type: 'string', nullable: true, unique: true)]
    #[Groups(['tag:read'])]
    private ?string $image = null;

    #[ORM\Column(type: 'string', nullable: true, unique: true)]
    #[Groups(['tag:read'])]
    private ?string $imageSmallThumbnail = null;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'tags')]
    #[Groups(['tag:read'])]
    private ?User $owner = null;

    #[ORM\ManyToOne(targetEntity: 'TagCategory', inversedBy: 'tags', fetch: 'EAGER', cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['tag:read', 'tag:write'])]
    #[ApiSubresource(maxDepth: 1)]
    private ?TagCategory $category = null;

    #[ORM\ManyToMany(targetEntity: 'Item', mappedBy: 'tags')]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $items;

    #[ORM\Column(type: 'integer')]
    #[Groups(['tag:read'])]
    private int $seenCounter;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups(['tag:read', 'tag:write'])]
    private string $visibility;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['tag:read'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['tag:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->items = new ArrayCollection();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
        $this->seenCounter = 0;
    }

    public function __toString(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getId(): ?string
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCategory(): ?TagCategory
    {
        return $this->category;
    }

    public function setCategory(?TagCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getItems(): DoctrineCollection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->addTag($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->removeTag($this);
        }

        return $this;
    }
}
