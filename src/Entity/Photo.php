<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\VisibilityEnum;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Album
 *
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="koi_photo", indexes={
 *     @ORM\Index(name="idx_photo_visibility", columns={"visibility"})
 * })
 */
class Photo
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
    private ?string $title = null;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $comment = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $place = null;

    /**
     * @var Album
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="photos")
     */
    private Album $album;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private ?User $owner = null;

    /**
     * @var Medium
     * @ORM\OneToOne(targetEntity="Medium", cascade={"all"}, orphanRemoval=true)
     */
    private ?Medium $image = null;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $takenAt = null;

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

    public function getImage(): ?Medium
    {
        return $this->image;
    }

    public function setImage(?Medium $image): self
    {
        if ($image->getThumbnailPath() === null) {
            $image->setMustGenerateAThumbnail(true);
        }

        $this->image = $image;

        return $this;
    }
}
