<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Attribute\Upload;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\CacheableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use App\Repository\ItemRepository;
use App\Validator as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\Table(name: 'koi_item')]
#[ORM\Index(name: 'idx_item_final_visibility', columns: ['final_visibility'])]
#[ApiResource(
    normalizationContext: ['groups' => ['item:read']],
    denormalizationContext: ['groups' => ['item:write']],
    collectionOperations: [
        'get',
        'post' => ['input_formats' => ['multipart' => ['multipart/form-data']]],
    ]
)]
class Item implements BreadcrumbableInterface, LoggableInterface, CacheableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['item:read'])]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Groups(['item:read', 'item:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\GreaterThan(0)]
    #[Groups(['item:read', 'item:write'])]
    private int $quantity;

    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'items')]
    #[Assert\NotBlank]
    #[Groups(['item:read', 'item:write'])]
    #[ApiSubresource(maxDepth: 1)]
    private ?Collection $collection = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['item:read'])]
    private ?User $owner = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'items', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'koi_item_tag')]
    #[ORM\JoinColumn(name: 'item_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id')]
    #[ORM\OrderBy(['label' => 'ASC'])]
    #[Groups(['item:write'])]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $tags;

    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'relatedTo')]
    #[ORM\JoinTable(name: 'koi_item_related_item')]
    #[ORM\JoinColumn(name: 'item_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'related_item_id', referencedColumnName: 'id')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    #[Groups(['item:write'])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $relatedItems;

    #[ORM\ManyToMany(targetEntity: Item::class, mappedBy: 'relatedItems')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private DoctrineCollection $relatedTo;

    #[ORM\OneToMany(targetEntity: Datum::class, mappedBy: 'item', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[ApiSubresource(maxDepth: 1)]
    #[AppAssert\UniqueDatumLabel]
    private DoctrineCollection $data;

    #[ORM\OneToMany(targetEntity: Loan::class, mappedBy: 'item', cascade: ['remove'])]
    #[ApiSubresource(maxDepth: 1)]
    private DoctrineCollection $loans;

    #[Upload(path: 'image',
        extraSmallThumbnailPath: 'imageExtraSmallThumbnail', extraSmallThumbnailSize: 60,
        smallThumbnailPath: 'imageSmallThumbnail', smallThumbnailSize: 150,
        largeThumbnailPath: 'imageLargeThumbnail', largeThumbnailSize: 300,
    )]
    #[Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/gif'])]
    #[Groups(['item:write'])]
    private ?File $file = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['item:read'])]
    private ?string $image = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['item:read'])]
    private ?string $imageExtraSmallThumbnail = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['item:read'])]
    private ?string $imageSmallThumbnail = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['item:read'])]
    private ?string $imageLargeThumbnail = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['item:read'])]
    private int $seenCounter;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['item:read', 'item:write'])]
    #[Assert\Choice(choices: VisibilityEnum::VISIBILITIES)]
    private string $visibility;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Groups(['item:read'])]
    private ?string $parentVisibility;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['item:read'])]
    private string $finalVisibility;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['item:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['item:read'])]
    private ?\DateTimeImmutable $updatedAt;

    private ?string $orderingValue = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->seenCounter = 0;
        $this->quantity = 1;
        $this->tags = new ArrayCollection();
        $this->data = new ArrayCollection();
        $this->relatedItems = new ArrayCollection();
        $this->visibility = VisibilityEnum::VISIBILITY_PUBLIC;
        $this->loans = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    public function getDatumByLabel(string $label): ?Datum
    {
        foreach ($this->getData() as $datum) {
            if ($datum->getLabel() === $label) {
                return $datum;
            }
        }

        return null;
    }

    public function getDataImages(): ArrayCollection
    {
        $criteria = Criteria::create();
        $criteria
            ->where(Criteria::expr()->in('type', DatumTypeEnum::IMAGE_TYPES))
            ->orderBy(['position' => Criteria::ASC])
        ;

        return $this->data->matching($criteria);
    }

    public function getDataTexts(): DoctrineCollection
    {
        $criteria = Criteria::create();
        $criteria
            ->where(Criteria::expr()->in('type', DatumTypeEnum::TEXT_TYPES))
            ->orderBy(['position' => Criteria::ASC])
        ;

        return $this->data->matching($criteria);
    }

    public function getTagWithValue(string $value): ?Tag
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('label', trim($value)));
        $tag = $this->tags->matching($criteria)->first();

        return $tag instanceof Tag ? $tag : null;
    }

    public function hasTag(Tag $tag): bool
    {
        return $this->tags->contains($tag);
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
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

    public function setRelatedItems(DoctrineCollection $relatedItems): self
    {
        $this->relatedItems = $relatedItems;

        return $this;
    }

    public function getRelatedItems(): DoctrineCollection
    {
        return $this->relatedItems;
    }

    public function getAllRelatedItems(): DoctrineCollection
    {
        return new ArrayCollection(array_merge($this->relatedItems->toArray(), $this->relatedTo->toArray()));
    }

    public function addRelatedItem(Item $relatedItem): self
    {
        if (!$this->relatedItems->contains($relatedItem)) {
            $this->relatedItems[] = $relatedItem;
        }

        return $this;
    }

    public function removeRelatedItem(Item $relatedItem): self
    {
        if ($this->relatedItems->contains($relatedItem)) {
            $this->relatedItems->removeElement($relatedItem);
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

        // Force Doctrine to trigger an update
        if ($file instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTimeImmutable());
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

    public function getImageExtraSmallThumbnail(): ?string
    {
        return $this->imageExtraSmallThumbnail;
    }

    public function setImageExtraSmallThumbnail(?string $imageExtraSmallThumbnail): Item
    {
        $this->imageExtraSmallThumbnail = $imageExtraSmallThumbnail;

        return $this;
    }

    public function getImageSmallThumbnail(): ?string
    {
        return $this->imageSmallThumbnail;
    }

    public function setImageSmallThumbnail(?string $imageSmallThumbnail): self
    {
        $this->imageSmallThumbnail = $imageSmallThumbnail;

        return $this;
    }

    public function getImageLargeThumbnail(): ?string
    {
        return $this->imageLargeThumbnail;
    }

    public function setImageLargeThumbnail(?string $imageLargeThumbnail): Item
    {
        $this->imageLargeThumbnail = $imageLargeThumbnail;

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

    public function getParentVisibility(): ?string
    {
        return $this->parentVisibility;
    }

    public function setParentVisibility(?string $parentVisibility): self
    {
        $this->parentVisibility = $parentVisibility;

        return $this;
    }

    public function getFinalVisibility(): string
    {
        return $this->finalVisibility;
    }

    public function setFinalVisibility(string $finalVisibility): self
    {
        $this->finalVisibility = $finalVisibility;

        return $this;
    }

    public function getOrderingValue(): ?string
    {
        return $this->orderingValue;
    }

    public function setOrderingValue(?string $orderingValue): Item
    {
        $this->orderingValue = $orderingValue;

        return $this;
    }
}
