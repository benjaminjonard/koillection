<?php

namespace App\Entity;

use App\Entity\Interfaces\BreabcrumbableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\VisibilityEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Collection
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\CollectionRepository")
 * @ORM\Table(name="koi_collection")
 */
class Collection implements LoggableInterface, BreabcrumbableInterface
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
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $childrenTitle;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $itemsTitle;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Collection", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"title" = "ASC"})
     */
    private $children;

    /**
     * @var \App\Entity\Collection
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="children")
     */
    private $parent;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="collections")
     */
    private $owner;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Item", mappedBy="collection", cascade={"all"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $items;

    /**
     * @var string
     * @ORM\Column(type="string", length=6)
     */
    private $color;

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

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->children = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getTitle() ?? '';
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
     * @return Collection
     */
    public function setTitle(string $title) : Collection
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
     * Add child.
     *
     * @param \App\Entity\Collection $child
     *
     * @return Collection
     */
    public function addChild(Collection $child) : Collection
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * @param Collection $child
     * @return Collection
     */
    public function removeChild(Collection $child) : Collection
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
     * @param \App\Entity\Collection $parent
     *
     * @return Collection
     */
    public function setParent(Collection $parent = null) : Collection
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return \App\Entity\Collection
     */
    public function getParent() : ?Collection
    {
        return $this->parent;
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
     * Add items.
     *
     * @param \App\Entity\Item $items
     *
     * @return Collection
     */
    public function addItem(Item $items) : Collection
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * @param Item $items
     * @return Collection
     */
    public function removeItem(Item $items) : Collection
    {
        $this->items->removeElement($items);

        return $this;
    }

    /**
     * Get items.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems() : DoctrineCollection
    {
        return $this->items;
    }

    /**
     * Set color.
     *
     * @param string $color
     *
     * @return Collection
     */
    public function setColor(string $color) : Collection
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
     * Set owner.
     *
     * @param \App\Entity\User $owner
     *
     * @return Collection
     */
    public function setOwner(User $owner = null) : Collection
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get items sorted naturally.
     *
     * @return array
     */
    public function getNaturallySortedItems() : array
    {
        $array = $this->items->toArray();
        usort($array, function(Item $a, Item $b) {
            return strnatcmp($a->getName(), $b->getName());
        });

        return $array;
    }

    /**
     * Set childrenTitle
     *
     * @param string $childrenTitle
     *
     * @return Collection
     */
    public function setChildrenTitle($childrenTitle)
    {
        $this->childrenTitle = $childrenTitle;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getChildrenTitle() : ?string
    {
        return $this->childrenTitle;
    }

    /**
     * Set itemsTitle
     *
     * @param string $itemsTitle
     *
     * @return Collection
     */
    public function setItemsTitle($itemsTitle)
    {
        $this->itemsTitle = $itemsTitle;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getItemsTitle() : ?string
    {
        return $this->itemsTitle;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Collection
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
     * @return Collection
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
     * Set image
     *
     * @param \App\Entity\Medium $image
     *
     * @return Collection
     */
    public function setImage(Medium $image = null) : Collection
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
