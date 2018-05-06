<?php

namespace App\Entity;

use App\Enum\VisibilityEnum;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Class Wish
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\WishRepository")
 * @ORM\Table(name="koi_wish")
 */
class Wish
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
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $price;

    /**
     * @var string
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $currency;

    /**
     * @var \App\Entity\Wishlist
     * @ORM\ManyToOne(targetEntity="Wishlist", inversedBy="wishes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $wishlist;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $owner;

    /**
     * @var string
     * @ORM\Column(type="string", length=6)
     */
    private $color;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var Medium
     * @ORM\OneToOne(targetEntity="Medium", cascade={"all"}, orphanRemoval=true)
     */
    private $image;

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
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Wish
     */
    public function setName(string $name) : Wish
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * Set wishlist.
     *
     * @param \App\Entity\Wishlist $wishlist
     *
     * @return Wish
     */
    public function setWishlist(Wishlist $wishlist = null) : Wish
    {
        $this->wishlist = $wishlist;

        return $this;
    }

    /**
     * Get wishlist.
     *
     * @return \App\Entity\Wishlist
     */
    public function getWishlist() : Wishlist
    {
        return $this->wishlist;
    }

    /**
     * Set owner.
     *
     * @param \App\Entity\User $owner
     *
     * @return Wish
     */
    public function setOwner(User $owner = null) : Wish
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner.
     *
     * @return User|null
     */
    public function getOwner() : ?User
    {
        return $this->owner;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Wish
     */
    public function setUrl(string $url) : Wish
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl() : ?string
    {
        return $this->url;
    }

    /**
     * Set price.
     *
     * @param string $price
     *
     * @return Wish
     */
    public function setPrice(string $price) : Wish
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice() : ?string
    {
        return $this->price;
    }

    /**
     * Set color.
     *
     * @param string $color
     *
     * @return Wish
     */
    public function setColor(string $color) : Wish
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string
     */
    public function getColor() : string
    {
        return $this->color;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     *
     * @return Wish
     */
    public function setComment(string $comment) : Wish
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment() : ?string
    {
        return $this->comment;
    }

    /**
     * Set image.
     *
     * @param Medium $image
     *
     * @return Wish
     */
    public function setImage(Medium $image = null) : Wish
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
     * @return Medium|null
     */
    public function getImage() : ?Medium
    {
        return $this->image;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Wish
     */
    public function setCurrency(string $currency) : Wish
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency() : ?string
    {
        return $this->currency;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Wish
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
     * @return Wish
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
