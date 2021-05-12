<?php

declare(strict_types=1);

namespace App\Entity;

use App\Annotation\Upload;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\CacheableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\VisibilityEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CollectionRepository")
 * @ORM\Table(name="koi_collection", indexes={
 *     @ORM\Index(name="idx_collection_visibility", columns={"visibility"})
 * })
 */
class Collection implements LoggableInterface, BreadcrumbableInterface, CacheableInterface
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $id;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private ?string $title = null;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $childrenTitle = null;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $itemsTitle = null;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Collection", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"title" = "ASC"})
     */
    private DoctrineCollection $children;

    /**
     * @var ?Collection
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="children")
     */
    private ?Collection $parent = null;

    /**
     * @var ?User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="collections")
     */
    private ?User $owner = null;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Item", mappedBy="collection", cascade={"all"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private DoctrineCollection $items;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Datum", mappedBy="collection", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private DoctrineCollection $data;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=6)
     */
    private ?string $color = null;

    /**
     * @var ?File
     * @Upload(path="image")
     */
    private ?File $file = null;

    /**
     * @var ?string
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $image = null;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private int $seenCounter;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $visibility;

    /**
     * @var ?\DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @var ?\DateTimeInterface
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->children = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->data = new ArrayCollection();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
        $this->seenCounter = 0;
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? '';
    }

    public function getNaturallySortedItems() : array
    {
        $array = $this->items->toArray();
        usort($array, function (Item $a, Item $b) {
            return strnatcmp($a->getName(), $b->getName());
        });

        return $array;
    }

    public function getId() : ?string
    {
        return $this->id->toString();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getChildrenTitle(): ?string
    {
        return $this->childrenTitle;
    }

    public function setChildrenTitle(?string $childrenTitle): self
    {
        $this->childrenTitle = $childrenTitle;

        return $this;
    }

    public function getItemsTitle(): ?string
    {
        return $this->itemsTitle;
    }

    public function setItemsTitle(?string $itemsTitle): self
    {
        $this->itemsTitle = $itemsTitle;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getSeenCounter(): ?int
    {
        return $this->seenCounter;
    }

    public function setSeenCounter(int $seenCounter): self
    {
        $this->seenCounter = $seenCounter;

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getChildren(): DoctrineCollection
    {
        return $this->children;
    }

    public function addChild(Collection $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Collection $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getItems(): DoctrineCollection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setCollection($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getCollection() === $this) {
                $item->setCollection(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;
        //Force Doctrine to trigger an update
        if ($file instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }

        return $this;
    }

    public function getData(): DoctrineCollection
    {
        return $this->data;
    }

    public function addData(Datum $data): self
    {
        if (!$this->data->contains($data)) {
            $this->data[] = $data;
            $data->setCollection($this);
        }

        return $this;
    }

    public function removeData(Datum $data): self
    {
        if ($this->data->contains($data)) {
            $this->data->removeElement($data);
            // set the owning side to null (unless already changed)
            if ($data->getCollection() === $this) {
                $data->setCollection(null);
            }
        }

        return $this;
    }
}
