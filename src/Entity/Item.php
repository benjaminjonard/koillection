<?php

namespace App\Entity;

use App\Entity\Interfaces\BreabcrumbableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use App\Model\BreadcrumbElement;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Item
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 * @ORM\Table(name="koi_item")
 */
class Item implements BreabcrumbableInterface, LoggableInterface
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
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $collection;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="items")
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
     * @ORM\OneToMany(targetEntity="Datum", mappedBy="item", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $data;

    /**
     * @var \App\Entity\Template
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="items")
     */
    private $template;

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
        $this->quantity = 1;
        $this->tags = new ArrayCollection();
        $this->data = new ArrayCollection();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
    }

    /**
     * Get entity breacrumb.
     *
     * @param User $user
     * @return BreadcrumbElement[]
     */
    public function getBreadcrumb($context) : array
    {
        $breadcrumb = [];
        $breadcrumbElement = new BreadcrumbElement();
        $breadcrumbElement->setType(BreadcrumbElement::TYPE_ENTITY)
            ->setLabel($this->getName())
            ->setRoute(\in_array($context, ['user', 'preview'], false) ? 'app_'.$context.'_item' : 'app_item_show')
            ->setEntity($this)
            ->setParams(['id' => $this->getId()]);

        if ($context === "user") {
            $breadcrumbElement->setParams(array_merge($breadcrumbElement->getParams(), ['username' => $this->getOwner()->getUsername()]));
        }

        $breadcrumb[] = $breadcrumbElement;

        if ($collection = $this->getCollection()) {
            $breadcrumb = array_merge($collection->getBreadcrumb($context), $breadcrumb);
        }

        return $breadcrumb;
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
    public function setName(string $name) : Item
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
    public function setCollection(Collection $collection = null) : Item
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
    public function setOwner(User $owner = null) : Item
    {
        $this->owner = $owner;

        return $this;
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
     * Set image
     *
     * @param \App\Entity\Medium $image
     *
     * @return Item
     */
    public function setImage(Medium $image = null) : Item
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
    public function addTag(Tag $tag) : Item
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
    public function removeTag(Tag $tag) : Item
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * Set tags.
     *
     * @return \App\Entity\Item $item
     */
    public function setTags(DoctrineCollection $tags) : Item
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
    public function addDatum(Datum $datum) : Item
    {
        $datum->setItem($this);
        $this->data->add($datum);

        return $this;
    }

    /**
     * @param Datum $datum
     * @return Item
     */
    public function removeDatum(Datum $datum) : Item
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
    public function setTemplate(Template $template = null) : Item
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
    public function setQuantity($quantity)
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
     * @return Item
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
