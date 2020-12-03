<?php

declare(strict_types=1);

namespace App\Entity;

use App\Annotation\Upload;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\CacheableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 * @ORM\Table(name="koi_item", indexes={
 *     @ORM\Index(name="idx_item_visibility", columns={"visibility"})
 * })
 */
class Item implements BreadcrumbableInterface, LoggableInterface, CacheableInterface
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
     */
    private ?string $name = null;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     */
    private int $quantity;

    /**
     * @var ?Collection
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="items")
     */
    private ?Collection $collection = null;

    /**
     * @var ?User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private ?User $owner = null;

    /**
     * @var DoctrineCollection
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="items", cascade={"persist"})
     * @ORM\JoinTable(
     *    name="koi_item_tag",
     *    joinColumns={@ORM\JoinColumn(name="item_id", referencedColumnName="id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"label" = "ASC"})
     */
    private DoctrineCollection $tags;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Datum", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private DoctrineCollection $data;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Loan", mappedBy="item", cascade={"remove"})
     */
    private DoctrineCollection $loans;

    /**
     * @var ?File
     * @Upload(path="image", smallThumbnailPath="imageSmallThumbnail")
     */
    private ?File $file = null;

    /**
     * @var ?string
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $image = null;

    /**
     * @var ?string
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $imageSmallThumbnail = null;

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
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @var ?\DateTimeInterface
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt;

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
        $this->loans = new ArrayCollection();
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
     * @return ArrayCollection
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
     * @return DoctrineCollection
     */
    public function getDataTexts() : DoctrineCollection
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('type', DatumTypeEnum::TYPE_TEXT))
            ->orWhere(Criteria::expr()->eq('type', DatumTypeEnum::TYPE_COUNTRY))
            ->orWhere(Criteria::expr()->eq('type', DatumTypeEnum::TYPE_FILE))
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

    public function hasTag(Tag $tag) : bool
    {
        return $this->tags->contains($tag);
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    public function setCollection(?Collection $collection): self
    {
        $this->collection = $collection;

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


    public function setTags(DoctrineCollection $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return DoctrineCollection|Tag[]
     */
    public function getTags(): DoctrineCollection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return DoctrineCollection|Datum[]
     */
    public function getData(): DoctrineCollection
    {
        return $this->data;
    }

    public function addData(Datum $data): self
    {
        if (!$this->data->contains($data)) {
            $this->data[] = $data;
            $data->setItem($this);
        }

        return $this;
    }

    public function removeData(Datum $data): self
    {
        if ($this->data->contains($data)) {
            $this->data->removeElement($data);
            // set the owning side to null (unless already changed)
            if ($data->getItem() === $this) {
                $data->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return DoctrineCollection|Loan[]
     */
    public function getLoans(): DoctrineCollection
    {
        return $this->loans;
    }

    public function addLoan(Loan $loan): self
    {
        if (!$this->loans->contains($loan)) {
            $this->loans[] = $loan;
            $loan->setItem($this);
        }

        return $this;
    }

    public function removeLoan(Loan $loan): self
    {
        if ($this->loans->contains($loan)) {
            $this->loans->removeElement($loan);
            // set the owning side to null (unless already changed)
            if ($loan->getItem() === $this) {
                $loan->setItem(null);
            }
        }

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
        if ($file) {
            $this->setUpdatedAt(new \DateTime());
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageSmallThumbnail(): ?string
    {
        if ($this->imageSmallThumbnail === null) {
            return $this->image;
        }

        return $this->imageSmallThumbnail;
    }

    public function setImageSmallThumbnail(?string $imageSmallThumbnail): self
    {
        $this->imageSmallThumbnail = $imageSmallThumbnail;

        return $this;
    }
}
