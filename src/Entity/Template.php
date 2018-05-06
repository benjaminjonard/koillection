<?php

namespace App\Entity;

use App\Entity\Interfaces\BreabcrumbableInterface;
use App\Model\BreadcrumbElement;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Template
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TemplateRepository")
 * @ORM\Table(name="koi_template")
 */
class Template implements BreabcrumbableInterface
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
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Field", mappedBy="template", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $fields;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Item", mappedBy="template", cascade={"all"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $items;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $owner;

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
        $this->fields = new ArrayCollection();
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
     * @return Template
     */
    public function setName(string $name) : Template
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
     * Add field.
     *
     * @param \App\Entity\Field $field
     *
     * @return Template
     */
    public function addField(Field $field) : Template
    {
        $field->setTemplate($this);
        $this->fields[] = $field;

        return $this;
    }

    /**
     * @param Field $field
     * @return Template
     */
    public function removeField(Field $field) : Template
    {
        $this->fields->removeElement($field);

        return $this;
    }

    /**
     * Get fields.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFields() : DoctrineCollection
    {
        return $this->fields;
    }

    /**
     * Set owner.
     *
     * @param \App\Entity\User $owner
     *
     * @return Template
     */
    public function setOwner(User $owner = null) : Template
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
     * @return Template
     */
    public function addItem(Item $item) : Template
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @param Item $item
     * @return Template
     */
    public function removeItem(Item $item) : Template
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Template
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
     * @return Template
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
}
