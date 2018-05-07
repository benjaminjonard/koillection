<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Medium
 *
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="koi_medium")
 */
class Medium
{
    const TYPE_IMAGE = 1;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $thumbnailPath;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $thumbnailSize;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $mimetype;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var UploadedFile
     */
    private $uploadedFile;

    /**
     * @var bool
     */
    private $mustGenerateAThumbnail;

    private $preventFileRemoval;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->type = self::TYPE_IMAGE;
        $this->mustGenerateAThumbnail = false;
        $this->preventFileRemoval = false;
    }

    public function preventFileRemoval()
    {
        $this->preventFileRemoval = true;

        return $this;
    }

    public function fileCanBeDeleted()
    {
        return !$this->preventFileRemoval;
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Medium
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return Medium
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Medium
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set thumbnailPath
     *
     * @param string $thumbnailPath
     *
     * @return Medium
     */
    public function setThumbnailPath($thumbnailPath)
    {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getThumbnailPath() : ?string
    {
        return $this->thumbnailPath;
    }

    /**
     * Set mimetype
     *
     * @param string $mimetype
     *
     * @return Medium
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * Get mimetype
     *
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Medium
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Medium
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set uploadedFile
     *
     * @param UploadedFile $uploadedFile
     *
     * @return Medium
     */
    public function setUploadedFile($uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }

    /**
     * Get uploadedFile
     *
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return Medium
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set thumbnailSize
     *
     * @param integer $thumbnailSize
     *
     * @return Medium
     */
    public function setThumbnailSize($thumbnailSize)
    {
        $this->thumbnailSize = $thumbnailSize;

        return $this;
    }

    /**
     * Get thumbnailSize
     *
     * @return integer
     */
    public function getThumbnailSize()
    {
        return $this->thumbnailSize;
    }

    public function setMustGenerateAThumbnail($mustGenerateAThumbnail)
    {
        $this->mustGenerateAThumbnail = $mustGenerateAThumbnail;

        return $this;
    }

    public function getMustGenerateAThumbnail()
    {
        return $this->mustGenerateAThumbnail;
    }

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="media")
     */
    private $owner;

    /**
     * Set owner
     *
     * @param \App\Entity\User $owner
     *
     * @return Medium
     */
    public function setOwner(\App\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User|null
     */
    public function getOwner() : ?User
    {
        return $this->owner;
    }
}
