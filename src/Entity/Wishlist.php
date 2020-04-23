<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Enum\VisibilityEnum;
use App\Model\BreadcrumbElement;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Class Wishlist
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\WishlistRepository")
 * @ORM\Table(name="koi_wishlist")
 */
class Wishlist implements BreadcrumbableInterface
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
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="wishlists")
     */
    private $owner;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Wish", mappedBy="wishlist", cascade={"all"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $wishes;

    /**
     * @var string
     * @ORM\Column(type="string", length=6)
     */
    private $color;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Wishlist", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $children;

    /**
     * @var \App\Entity\Wishlist
     * @ORM\ManyToOne(targetEntity="Wishlist", inversedBy="children")
     */
    private $parent;

    /**
     * @var Medium
     * @ORM\OneToOne(targetEntity="Medium", cascade={"all"}, orphanRemoval=true)
     */
    private $image;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $seenCounter;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $visibility;

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
     * Constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->wishes = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
        $this->seenCounter = 0;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() ?? '';
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
     * @return Wishlist
     */
    public function setName(string $name) : self
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
     * Set owner.
     *
     * @param \App\Entity\User $owner
     *
     * @return Wishlist
     */
    public function setOwner(User $owner = null) : self
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
     * Add wish.
     *
     * @param \App\Entity\Wish $wish
     *
     * @return Wishlist
     */
    public function addWish(Wish $wish) : self
    {
        $this->wishes[] = $wish;

        return $this;
    }

    /**
     * @param Wish $wish
     * @return Wishlist
     */
    public function removeWish(Wish $wish) : self
    {
        $this->wishes->removeElement($wish);

        return $this;
    }

    /**
     * Get wishes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWishes() : DoctrineCollection
    {
        return $this->wishes;
    }

    /**
     * Set color.
     *
     * @param string $color
     *
     * @return Wishlist
     */
    public function setColor(string $color) : self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string
     */
    public function getColor() : ?string
    {
        return $this->color;
    }

    /**
     * Add child.
     *
     * @param \App\Entity\Wishlist $child
     *
     * @return Wishlist
     */
    public function addChild(Wishlist $child) : self
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * @param Wishlist $child
     * @return Wishlist
     */
    public function removeChild(Wishlist $child) : self
    {
        $this->children->removeElement($child);

        return $this;
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren() : DoctrineCollection
    {
        return $this->children;
    }

    /**
     * Set parent.
     *
     * @param \App\Entity\Wishlist $parent
     *
     * @return Wishlist
     */
    public function setParent(Wishlist $parent = null) : self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return \App\Entity\Wishlist
     */
    public function getParent() : ?Wishlist
    {
        return $this->parent;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Wishlist
     */
    public function setCreatedAt($createdAt) : self
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
     * @return Wishlist
     */
    public function setUpdatedAt($updatedAt) : self
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
     * Set image
     *
     * @param \App\Entity\Medium $image
     *
     * @return Wishlist
     */
    public function setImage(Medium $image = null) : self
    {
        if ($image === null) {
            return $this;
        }

        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \App\Entity\Medium
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getVisibility() : string
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     * @return Wishlist
     */
    public function setVisibility(string $visibility) : self
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @return int
     */
    public function getSeenCounter() : int
    {
        return $this->seenCounter;
    }

    /**
     * @param int $seenCounter
     * @return Wishlist
     */
    public function setSeenCounter(int $seenCounter) : self
    {
        $this->seenCounter = $seenCounter;

        return $this;
    }
}
