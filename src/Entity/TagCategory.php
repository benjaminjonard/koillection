<?php

declare(strict_types=1);

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
 * Class TagCategory
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TagCategoryRepository")
 * @ORM\Table(name="koi_tag_category")
 */
class TagCategory implements BreadcrumbableInterface
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
     * @var string
     * @ORM\Column(type="string", length=7)
     */
    private $color;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tagCategories")
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="category")
     */
    private $tags;

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
     * @return string
     */
    public function getLabel() : ?string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return TagCategory
     */
    public function setLabel(string $label) : TagCategory
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return TagCategory
     */
    public function setDescription(string $description) : TagCategory
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor() : ?string
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return TagCategory
     */
    public function setColor(string $color) : TagCategory
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner() : ?User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     * @return TagCategory
     */
    public function setOwner(User $owner) : TagCategory
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() : ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return TagCategory
     */
    public function setCreatedAt(\DateTime $createdAt) : TagCategory
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt() : ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return TagCategory
     */
    public function setUpdatedAt(\DateTime $updatedAt) : TagCategory
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
