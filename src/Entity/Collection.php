<?php

declare(strict_types=1);

namespace App\Entity;

use Api\Controller\UploadController;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Attribute\Upload;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\CacheableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Enum\DatumTypeEnum;
use App\Enum\DisplayModeEnum;
use App\Enum\SortingDirectionEnum;
use App\Enum\VisibilityEnum;
use App\Repository\CollectionRepository;
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

#[ORM\Entity(repositoryClass: CollectionRepository::class)]
#[ORM\Table(name: 'koi_collection')]
#[ORM\Index(name: 'idx_collection_final_visibility', columns: ['final_visibility'])]
#[ApiResource(
    denormalizationContext: ['groups' => ['collection:write']],
    normalizationContext: ['groups' => ['collection:read']],
    operations: [
        new Get(),
        new Put(),
        new Delete(),
        new Patch(),
        new GetCollection(),
        new Post(inputFormats: ['json' => ['application/json', 'application/ld+json'], 'multipart' => ['multipart/form-data']]),
        new Post(uriTemplate: '/collections/{id}/image', controller: UploadController::class, denormalizationContext: ['groups' => ['collection:image']], inputFormats: ['multipart' => ['multipart/form-data']], openapiContext: ['summary' => 'Upload the Collection image.'])
    ]
)]
#[ApiResource(uriTemplate: '/collections/{id}/children', uriVariables: ['id' => new Link(fromClass: Collection::class, fromProperty: 'children')], normalizationContext: ['groups' => ['collection:read']], operations: [new GetCollection()])]
#[ApiResource(uriTemplate: '/collections/{id}/parent', uriVariables: ['id' => new Link(fromClass: Collection::class, fromProperty: 'parent')], normalizationContext: ['groups' => ['collection:read']], operations: [new Get()])]
#[ApiResource(uriTemplate: '/data/{id}/collection', uriVariables: ['id' => new Link(fromClass: Datum::class, fromProperty: 'collection')], normalizationContext: ['groups' => ['collection:read']], operations: [new Get()])]
#[ApiResource(uriTemplate: '/items/{id}/collection', uriVariables: ['id' => new Link(fromClass: Item::class, fromProperty: 'collection')], normalizationContext: ['groups' => ['collection:read']], operations: [new Get()])]
class Collection implements LoggableInterface, BreadcrumbableInterface, CacheableInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['collection:read'])]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Groups(['collection:read', 'collection:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['collection:read', 'collection:write'])]
    private ?string $childrenTitle = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['collection:read', 'collection:write'])]
    private ?string $itemsTitle = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\OneToMany(targetEntity: Collection::class, mappedBy: 'parent', cascade: ['all'])]
    #[ORM\OrderBy(['title' => Criteria::ASC])]
    private DoctrineCollection $children;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'children')]
    #[Groups(['collection:read', 'collection:write'])]
    #[Assert\Expression('not (value == this)', message: 'error.parent.same_as_current_object')]
    private ?Collection $parent = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'collections')]
    #[Groups(['collection:read'])]
    private ?User $owner = null;

    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'collection', cascade: ['all'])]
    private DoctrineCollection $items;

    #[ORM\OneToMany(targetEntity: Datum::class, mappedBy: 'collection', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => Criteria::ASC])]
    #[AppAssert\UniqueDatumLabel]
    private DoctrineCollection $data;

    #[ORM\Column(type: Types::STRING, length: 6)]
    #[Groups(['collection:read'])]
    private ?string $color = null;

    #[Upload(path: 'image', maxWidth: 200, maxHeight: 200)]
    #[Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/webp'])]
    #[Groups(['collection:write', 'collection:image'])]
    private ?File $file = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['collection:read'])]
    private ?string $image = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['collection:read'])]
    private int $seenCounter = 0;

    #[ORM\Column(type: Types::STRING, length: 4)]
    #[Groups(['collection:read', 'collection:write'])]
    #[Assert\Choice(choices: DisplayModeEnum::DISPLAY_MODES)]
    private string $childrenDisplayMode = DisplayModeEnum::DISPLAY_MODE_GRID;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    #[Groups(['collection:read', 'collection:write'])]
    private bool $childrenListShowVisibility = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    #[Groups(['collection:read', 'collection:write'])]
    private bool $childrenListShowActions = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    #[Groups(['collection:read', 'collection:write'])]
    private bool $childrenListShowNumberOfChildren = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    #[Groups(['collection:read', 'collection:write'])]
    private bool $childrenListShowNumberOfItems = true;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['collection:read', 'collection:write'])]
    private ?string $childrenSortingProperty = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Assert\Choice(choices: DatumTypeEnum::AVAILABLE_FOR_ORDERING)]
    #[Groups(['collection:read', 'collection:write'])]
    private ?string $childrenSortingType = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['collection:read', 'collection:write'])]
    #[Assert\Choice(choices: SortingDirectionEnum::SORTING_DIRECTIONS)]
    private ?string $childrenSortingDirection = Criteria::ASC;

    #[ORM\ManyToOne(targetEntity: Template::class)]
    #[Groups(['item:read', 'item:write'])]
    private ?Template $itemsDefaultTemplate = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['collection:read', 'collection:write'])]
    private ?string $itemsSortingProperty = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Assert\Choice(choices: DatumTypeEnum::AVAILABLE_FOR_ORDERING)]
    #[Groups(['collection:read', 'collection:write'])]
    private ?string $itemsSortingType = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['collection:read', 'collection:write'])]
    #[Assert\Choice(choices: SortingDirectionEnum::SORTING_DIRECTIONS)]
    private ?string $itemsSortingDirection = Criteria::ASC;

    #[ORM\Column(type: Types::STRING, length: 4)]
    #[Groups(['collection:read', 'collection:write'])]
    #[Assert\Choice(choices: DisplayModeEnum::DISPLAY_MODES)]
    private string $itemsDisplayMode = DisplayModeEnum::DISPLAY_MODE_GRID;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    #[Groups(['collection:read', 'collection:write'])]
    private ?array $itemsListColumns = [];

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    #[Groups(['collection:read', 'collection:write'])]
    private bool $itemsListShowVisibility = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    #[Groups(['collection:read', 'collection:write'])]
    private bool $itemsListShowActions = true;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['collection:read', 'collection:write'])]
    #[Assert\Choice(choices: VisibilityEnum::VISIBILITIES)]
    private string $visibility = VisibilityEnum::VISIBILITY_PUBLIC;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Groups(['collection:read'])]
    private ?string $parentVisibility = null;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['collection:read'])]
    private string $finalVisibility;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['collection:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['collection:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->children = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->data = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? '';
    }

    public function getNaturallySortedItems(): array
    {
        $array = $this->items->toArray();
        usort($array, static function (Item $a, Item $b): int {
            return strnatcmp($a->getName(), $b->getName());
        });

        return $array;
    }

    public function getId(): ?string
    {
        return $this->id;
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
        // Force Doctrine to trigger an update
        if ($file instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTimeImmutable());
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

    public function getChildrenDisplayMode(): string
    {
        return $this->childrenDisplayMode;
    }

    public function setChildrenDisplayMode(string $childrenDisplayMode): Collection
    {
        $this->childrenDisplayMode = $childrenDisplayMode;

        return $this;
    }

    public function getItemsDefaultTemplate(): ?Template
    {
        return $this->itemsDefaultTemplate;
    }

    public function setItemsDefaultTemplate(?Template $itemsDefaultTemplate): Collection
    {
        $this->itemsDefaultTemplate = $itemsDefaultTemplate;

        return $this;
    }

    public function getItemsDisplayMode(): string
    {
        return $this->itemsDisplayMode;
    }

    public function setItemsDisplayMode(string $itemsDisplayMode): Collection
    {
        $this->itemsDisplayMode = $itemsDisplayMode;

        return $this;
    }

    public function getItemsSortingProperty(): ?string
    {
        return $this->itemsSortingProperty;
    }

    public function setItemsSortingProperty(?string $itemsSortingProperty): Collection
    {
        $this->itemsSortingProperty = $itemsSortingProperty;

        return $this;
    }

    public function getItemsSortingDirection(): ?string
    {
        return $this->itemsSortingDirection;
    }

    public function getItemsSortingType(): ?string
    {
        return $this->itemsSortingType;
    }

    public function setItemsSortingType(?string $itemsSortingType): Collection
    {
        $this->itemsSortingType = $itemsSortingType;

        return $this;
    }

    public function setItemsSortingDirection(?string $itemsSortingDirection): Collection
    {
        $this->itemsSortingDirection = $itemsSortingDirection;

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

    public function getItemsListColumns(): ?array
    {
        return $this->itemsListColumns;
    }

    public function setItemsListColumns(?array $itemsListColumns): Collection
    {
        $this->itemsListColumns = $itemsListColumns;

        return $this;
    }

    public function getItemsListShowVisibility(): bool
    {
        return $this->itemsListShowVisibility;
    }

    public function setItemsListShowVisibility(bool $itemsListShowVisibility): Collection
    {
        $this->itemsListShowVisibility = $itemsListShowVisibility;

        return $this;
    }

    public function getItemsListShowActions(): bool
    {
        return $this->itemsListShowActions;
    }

    public function setItemsListShowActions(bool $itemsListShowActions): Collection
    {
        $this->itemsListShowActions = $itemsListShowActions;

        return $this;
    }

    public function isChildrenListShowVisibility(): bool
    {
        return $this->childrenListShowVisibility;
    }

    public function setChildrenListShowVisibility(bool $childrenListShowVisibility): Collection
    {
        $this->childrenListShowVisibility = $childrenListShowVisibility;

        return $this;
    }

    public function isChildrenListShowActions(): bool
    {
        return $this->childrenListShowActions;
    }

    public function setChildrenListShowActions(bool $childrenListShowActions): Collection
    {
        $this->childrenListShowActions = $childrenListShowActions;

        return $this;
    }

    public function getChildrenSortingProperty(): ?string
    {
        return $this->childrenSortingProperty;
    }

    public function setChildrenSortingProperty(?string $childrenSortingProperty): Collection
    {
        $this->childrenSortingProperty = $childrenSortingProperty;

        return $this;
    }

    public function getChildrenSortingType(): ?string
    {
        return $this->childrenSortingType;
    }

    public function setChildrenSortingType(?string $childrenSortingType): Collection
    {
        $this->childrenSortingType = $childrenSortingType;

        return $this;
    }

    public function getChildrenSortingDirection(): ?string
    {
        return $this->childrenSortingDirection;
    }

    public function setChildrenSortingDirection(?string $childrenSortingDirection): Collection
    {
        $this->childrenSortingDirection = $childrenSortingDirection;

        return $this;
    }

    public function isChildrenListShowNumberOfChildren(): bool
    {
        return $this->childrenListShowNumberOfChildren;
    }

    public function setChildrenListShowNumberOfChildren(bool $childrenListShowNumberOfChildren): Collection
    {
        $this->childrenListShowNumberOfChildren = $childrenListShowNumberOfChildren;

        return $this;
    }

    public function isChildrenListShowNumberOfItems(): bool
    {
        return $this->childrenListShowNumberOfItems;
    }

    public function setChildrenListShowNumberOfItems(bool $childrenListShowNumberOfItems): Collection
    {
        $this->childrenListShowNumberOfItems = $childrenListShowNumberOfItems;

        return $this;
    }
}
