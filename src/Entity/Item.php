<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Item
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 * @ORM\Table(name="koi_item", indexes={
 *     @ORM\Index(name="idx_item_visibility", columns={"visibility"})
 * })
 */
class Item implements BreadcrumbableInterface, LoggableInterface
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
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     */
    private $quantity;

    /**
     * @var \App\Entity\Collection
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="items")
     */
    private $collection;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $owner;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="items", cascade={"persist"})
     * @ORM\JoinTable(
     *    name="koi_item_tag",
     *    joinColumns={@ORM\JoinColumn(name="item_id", referencedColumnName="id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"label" = "ASC"})
     */
    private $tags;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Datum", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $data;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Loan", mappedBy="item", cascade={"remove"})
     */
    private $loans;

    /**
     * @var \App\Entity\Template
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="items")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $template;

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
        $this->seenCounter = 0;
        $this->quantity = 1;
        $this->tags = new ArrayCollection();
        $this->data = new ArrayCollection();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    /**
     * Get Data of type images.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDataImages() : ArrayCollection
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('type', DatumTypeEnum::TYPE_SIGN))
            ->orWhere(Criteria::expr()->eq('type', DatumTypeEnum::TYPE_IMAGE))
            ->orderBy(['position' => Criteria::ASC]);

        return $this->data->matching($criteria);
    }

    /**
     * Get Data of type text.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDataTexts() : DoctrineCollection
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('type', DatumTypeEnum::TYPE_TEXT))
            ->orWhere(Criteria::expr()->eq('type', DatumTypeEnum::TYPE_COUNTRY))
            ->orderBy(['position' => Criteria::ASC]);

        return $this->data->matching($criteria);
    }

    public function getTagWithValue(string $value) : ?Tag
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('label', trim($value)));
        $tag = $this->tags->matching($criteria)->first();

        return $tag instanceof Tag ? $tag : null;
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
     * @return Item
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
     * Set collection.
     *
     * @param \App\Entity\Collection $collection
     *
     * @return Item
     */
    public function setCollection(Collection $collection = null) : self
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection.
     *
     * @return \App\Entity\Collection
     */
    public function getCollection() : ?Collection
    {
        return $this->collection;
    }

    /**
     * Set owner.
     *
     * @param \App\Entity\User $owner
     *
     * @return Item
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
     * Set image
     *
     * @param \App\Entity\Medium $image
     *
     * @return Item
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
     * @return \App\Entity\Medium
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add tags.
     *
     * @param \App\Entity\Tag $tag
     *
     * @return Item
     */
    public function addTag(Tag $tag) : self
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tags.
     *
     * @param \App\Entity\Tag $tag
     *
     * @return Item
     */
    public function removeTag(Tag $tag) : self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * Set tags.
     *
     * @return \App\Entity\Item $item
     */
    public function setTags(DoctrineCollection $tags) : self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags.
     */
    public function getTags() : DoctrineCollection
    {
        return $this->tags;
    }

    /**
     * Return true id collection has the specified tag.
     *
     * @param \App\Entity\Tag $tag
     *
     * @return bool
     */
    public function hasTag(Tag $tag) : bool
    {
        return $this->tags->contains($tag);
    }

    /**
     * Add datum.
     *
     * @param \App\Entity\Datum $datum
     *
     * @return Item
     */
    public function addData(Datum $datum) : self
    {
        $datum->setItem($this);
        $this->data->add($datum);

        return $this;
    }

    /**
     * @param Datum $datum
     * @return Item
     */
    public function removeData(Datum $datum) : self
    {
        $this->data->removeElement($datum);

        return $this;
    }

    /**
     * Get data.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getData() : DoctrineCollection
    {
        return $this->data;
    }

    /**
     * Set template.
     *
     * @param \App\Entity\Template $template
     *
     * @return Item
     */
    public function setTemplate(Template $template = null) : self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template.
     *
     * @return \App\Entity\Template
     */
    public function getTemplate() : ?Template
    {
        return $this->template;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Item
     */
    public function setQuantity($quantity) : self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Item
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
     * @return Item
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
     * @return Item
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
     * @return Item
     */
    public function setSeenCounter(int $seenCounter) : self
    {
        $this->seenCounter = $seenCounter;

        return $this;
    }
}
