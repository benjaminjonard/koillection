<?php

declare(strict_types=1);

namespace App\Entity;

use App\Annotation\Upload;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DatumRepository")
 * @ORM\Table(name="koi_datum", indexes={
 *     @ORM\Index(name="idx_datum_visibility", columns={"visibility"})
 * })
 */
class Datum implements LoggableInterface
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $id;

    /**
     * @var Item
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="data")
     */
    private ?Item $item;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private ?string $type = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $label = null;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $value = null;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $position = null;

    /**
     * @var File
     * @Upload(path="image", smallThumbnailPath="imageSmallThumbnail", mediumThumbnailPath="imageMediumThumbnail")
     */
    private ?File $file = null;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $image = null;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $imageSmallThumbnail = null;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $imageMediumThumbnail = null;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private ?User $owner = null;

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
    private ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getLabel() ?? '';
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;
        //Force Doctrine to trigger an update
        $this->setUpdatedAt(new \DateTime());

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

    public function getImageMediumThumbnail(): ?string
    {
        if ($this->imageMediumThumbnail === null) {
            return $this->image;
        }

        return $this->imageMediumThumbnail;
    }

    public function setImageMediumThumbnail(?string $imageMediumThumbnail): self
    {
        $this->imageMediumThumbnail = $imageMediumThumbnail;

        return $this;
    }
}
