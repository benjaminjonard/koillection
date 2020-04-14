<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Image
 *
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="koi_image")
 */
class Image
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
    private ?string $filename = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private ?string $path = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $thumbnailPath = null;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private ?int $size = null;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $thumbnailSize = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private ?string $mimetype = null;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private ?User $owner = null;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var UploadedFile
     */
    private ?UploadedFile $uploadedFile = null;

    /**
     * @var bool
     */
    private bool $mustGenerateAThumbnail = false;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getThumbnailPath(): ?string
    {
        return $this->thumbnailPath;
    }

    public function setThumbnailPath(?string $thumbnailPath): self
    {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getThumbnailSize(): ?int
    {
        return $this->thumbnailSize;
    }

    public function setThumbnailSize(?int $thumbnailSize): self
    {
        $this->thumbnailSize = $thumbnailSize;

        return $this;
    }

    public function getMimetype(): ?string
    {
        return $this->mimetype;
    }

    public function setMimetype(string $mimetype): self
    {
        $this->mimetype = $mimetype;

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

    public function setUploadedFile($uploadedFile) : self
    {
        $this->uploadedFile = $uploadedFile;
        $this->setUpdatedAt(new \DateTime()); //Hack for doctrine, uploadedFile is not mapped so Doctrine doesn't see it changed

        if ($this->getThumbnailPath()) {
            $this->mustGenerateAThumbnail = true;
        }

        return $this;
    }

    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    public function setMustGenerateAThumbnail($mustGenerateAThumbnail) : self
    {
        $this->mustGenerateAThumbnail = $mustGenerateAThumbnail;

        return $this;
    }

    public function getMustGenerateAThumbnail()
    {
        return $this->mustGenerateAThumbnail;
    }
}
