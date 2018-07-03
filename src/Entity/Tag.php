<?php

namespace App\Entity;

use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\VisibilityEnum;
use App\Model\BreadcrumbElement;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Tag
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @ORM\Table(name="koi_tag")
 */
class Tag implements BreadcrumbableInterface, LoggableInterface
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
    private $label;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var Medium
     * @ORM\OneToOne(targetEntity="Medium", cascade={"all"}, orphanRemoval=true)
     */
    private $image;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tags")
     */
    private $owner;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Item", mappedBy="tags")
     */
    private $items;

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
        $this->items = new ArrayCollection();
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

    /**
     * Set label.
     *
     * @param string $label
     *
     * @return Tag
     */
    public function setLabel(string $label) : self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel() : string
    {
        return $this->label;
    }

    /**
     * Set owner.
     *
     * @param \App\Entity\User $owner
     *
     * @return Tag
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
     * Add item.
     *
     * @param \App\Entity\Item $item
     *
     * @return Tag
     */
    public function addItem(Item $item) : self
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @param Item $item
     * @return Tag
     */
    public function removeItem(Item $item) : self
    {
        $this->items->removeElement($item);

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
     * Set description
     *
     * @param string $description
     *
     * @return Tag
     */
    public function setDescription(?string $description) : self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param Medium $image
     *
     * @return Tag
     */
    public function setImage(Medium $image = null) : self
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
     * Get image
     *
     * @return Medium
     */
    public function getImage() : ?Medium
    {
        return $this->image;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Tag
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
     * @return Tag
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
     * @return string
     */
    public function getVisibility() : string
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     * @return Tag
     */
    public function setVisibility(string $visibility) : self
    {
        $this->visibility = $visibility;

        return $this;
    }
}
