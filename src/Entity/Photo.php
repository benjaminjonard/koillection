<?php

namespace App\Entity;

use App\Entity\Interfaces\BreabcrumbableInterface;
use App\Enum\VisibilityEnum;
use App\Model\BreadcrumbElement;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Album
 *
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="koi_photo")
 */
class Photo implements BreabcrumbableInterface
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $place;

    /**
     * @var \App\Entity\Album
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="photos")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $album;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="photos")
     */
    private $owner;

    /**
     * @var Medium
     * @ORM\OneToOne(targetEntity="Medium", cascade={"all"}, orphanRemoval=true)
     */
    private $image;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $takenAt;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    protected $visibility;

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

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
    }

    /**
     * Get entity breacrumb.
     *
     * @return BreadcrumbElement[]
     */
    public function getBreadcrumb($context) : array
    {
        $breadcrumb = [];
        $breadcrumbElement = new BreadcrumbElement();
        $breadcrumbElement->setType(BreadcrumbElement::TYPE_ENTITY)
            ->setLabel($this->getTitle())
            ->setRoute('app_album_show')
            ->setEntity($this)
            ->setParams(['id' => $this->getAlbum()->getId()]);
        $breadcrumb[] = $breadcrumbElement;

        return $breadcrumb;
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Photo
     */
    public function setTitle(string $title) : Photo
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return ?string
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     *
     * @return Photo
     */
    public function setComment(string $comment) : Photo
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return ?string
     */
    public function getComment() : ?string
    {
        return $this->comment;
    }

    /**
     * Set palce.
     *
     * @param string $place
     *
     * @return Photo
     */
    public function setPlace(string $place) : Photo
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place.
     *
     * @return ?string
     */
    public function getPlace() : ?string
    {
        return $this->place;
    }

    /**
     * Set album.
     *
     * @param \App\Entity\Album $album
     *
     * @return Photo
     */
    public function setAlbum(Album $album = null) : Photo
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album.
     *
     * @return \App\Entity\Album
     */
    public function getAlbum() : Album
    {
        return $this->album;
    }

    /**
     * Get owner.
     *
     * @return \App\Entity\User
     */
    public function getOwner() : ?User
    {
        return $this->owner;
    }

    /**
     * Set owner.
     *
     * @param \App\Entity\User $owner
     *
     * @return Photo
     */
    public function setOwner(User $owner = null) : Photo
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Set image.
     *
     * @param Medium $image
     *
     * @return Photo
     */
    public function setImage(Medium $image = null) : Photo
    {
        if ($image === null) {
            return $this;
        }

        if ($image->getThumbnailPath() === null) {
            $image->setMustGenerateAThumbnail(true);
        }

        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return Medium
     */
    public function getImage() : ?Medium
    {
        return $this->image;
    }

    /**
     * Set takenAt
     *
     * @param \DateTime $takenAt
     *
     * @return Photo
     */
    public function setTakenAt($takenAt) : Photo
    {
        $this->takenAt = $takenAt;

        return $this;
    }

    /**
     * Get takenAt
     *
     * @return \DateTime
     */
    public function getTakenAt()
    {
        return $this->takenAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Photo
     */
    public function setCreatedAt($createdAt) : Photo
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
     * @return Photo
     */
    public function setUpdatedAt($updatedAt) : Photo
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
     * @return int
     */
    public function getVisibility() : int
    {
        return $this->visibility;
    }

    /**
     * @param int $visibility
     * @return $this
     */
    public function setVisibility(int $visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }
}
