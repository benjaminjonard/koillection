<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Attribute\Upload;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\DatumTypeEnum;
use App\Repository\DatumRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DatumRepository::class)]
#[ORM\Table(name: "koi_datum")]
#[ApiResource(
    normalizationContext: ['groups' => ["datum:read"]],
    denormalizationContext: ["groups" => ["datum:write"]],
)]
class Datum implements LoggableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36, unique: true, options: ["fixed" => true])]
    #[Groups(["datum:read"])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: "Item", inversedBy: "data")]
    #[Groups(["datum:read"])]
    private ?Item $item = null;

    #[ORM\ManyToOne(targetEntity: "Collection", inversedBy: "data")]
    #[Groups(["datum:read"])]
    private ?Collection $collection = null;

    #[ORM\Column(type: "string", length: 10)]
    #[Groups(["datum:read", "datum:write"])]
    #[Assert\Choice(choices: DatumTypeEnum::TYPES)]
    private ?string $type = null;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["datum:read", "datum:write"])]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["datum:read", "datum:write"])]
    private ?string $value = null;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Groups(["datum:read", "datum:write"])]
    private ?int $position = null;

    #[Upload(path: "file", smallThumbnailPath: "imageSmallThumbnail", largeThumbnailPath: "imageLargeThumbnail")]
    private ?File $fileImage = null;

    #[ORM\Column(type: "string", nullable: true, unique: true)]
    #[Groups(["datum:read"])]
    private ?string $image = null;

    #[ORM\Column(type: "string", nullable: true, unique: true)]
    #[Groups(["datum:read"])]
    private ?string $imageSmallThumbnail = null;

    #[ORM\Column(type: "string", nullable: true, unique: true)]
    #[Groups(["datum:read"])]
    private ?string $imageLargeThumbnail = null;

    #[Upload(path: "file", originalFilenamePath: "originalFilename")]
    private ?File $fileFile = null;

    #[ORM\Column(type: "string", nullable: true, unique: true)]
    #[Groups(["datum:read"])]
    private ?string $file = null;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["datum:read"])]
    private ?string $originalFilename = null;

    #[ORM\ManyToOne(targetEntity: "User")]
    #[Groups(["datum:read"])]
    private ?User $owner = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(["datum:read"])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Groups(["datum:read"])]
    private ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function __toString(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

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

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

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

    public function getFileImage(): ?File
    {
        return $this->fileImage;
    }

    public function setFileImage(?File $fileImage): self
    {
        $this->fileImage = $fileImage;
        //Force Doctrine to trigger an update
        if ($fileImage instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }

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

    public function getImageSmallThumbnail(): ?string
    {
        if ($this->imageSmallThumbnail) {
            return $this->imageSmallThumbnail;
        }

        if ($this->imageLargeThumbnail) {
            return $this->imageLargeThumbnail;
        }

        return $this->image;
    }

    public function setImageSmallThumbnail(?string $imageSmallThumbnail): self
    {
        $this->imageSmallThumbnail = $imageSmallThumbnail;

        return $this;
    }

    public function getImageLargeThumbnail(): ?string
    {
        if ($this->imageLargeThumbnail === null) {
            return $this->image;
        }

        return $this->imageLargeThumbnail;
    }

    public function setImageLargeThumbnail(?string $imageLargeThumbnail): self
    {
        $this->imageLargeThumbnail = $imageLargeThumbnail;

        return $this;
    }

    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    public function setCollection(?Collection $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function getFileFile(): ?File
    {
        return $this->fileFile;
    }

    public function setFileFile(?File $fileFile): Datum
    {
        $this->fileFile = $fileFile;
        //Force Doctrine to trigger an update
        if ($fileFile instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): Datum
    {
        $this->file = $file;
        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): Datum
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }
}
