<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\DatumTypeEnum;
use App\Enum\DisplayModeEnum;
use App\Enum\SortingDirectionEnum;
use App\Repository\DisplayConfigurationRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DisplayConfigurationRepository::class)]
#[ORM\Table(name: 'koi_display_configuration')]
class DisplayConfiguration
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $owner = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(type: Types::STRING, length: 4)]
    #[Assert\Choice(choices: DisplayModeEnum::DISPLAY_MODES)]
    private string $displayMode = DisplayModeEnum::DISPLAY_MODE_GRID;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $sortingProperty = null;

    #[ORM\Column(type: Types::STRING, length: 15, nullable: true)]
    #[Assert\Choice(choices: DatumTypeEnum::TEXT_TYPES)]
    private ?string $sortingType = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\Choice(choices: SortingDirectionEnum::SORTING_DIRECTIONS)]
    private ?string $sortingDirection = Order::Ascending->value;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    private bool $showVisibility = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    private bool $showActions = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    private bool $showNumberOfChildren = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    private bool $showNumberOfItems = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $showItemQuantities = false;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $columns = [];

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): DisplayConfiguration
    {
        $this->owner = $owner;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): DisplayConfiguration
    {
        $this->label = $label;

        return $this;
    }

    public function getDisplayMode(): string
    {
        return $this->displayMode;
    }

    public function setDisplayMode(string $displayMode): DisplayConfiguration
    {
        $this->displayMode = $displayMode;

        return $this;
    }

    public function getSortingProperty(): ?string
    {
        return $this->sortingProperty;
    }

    public function setSortingProperty(?string $sortingProperty): DisplayConfiguration
    {
        $this->sortingProperty = $sortingProperty;

        return $this;
    }

    public function getSortingType(): ?string
    {
        return $this->sortingType;
    }

    public function setSortingType(?string $sortingType): DisplayConfiguration
    {
        $this->sortingType = $sortingType;

        return $this;
    }

    public function getSortingDirection(): ?string
    {
        return $this->sortingDirection;
    }

    public function setSortingDirection(?string $sortingDirection): DisplayConfiguration
    {
        $this->sortingDirection = $sortingDirection;

        return $this;
    }

    public function isShowVisibility(): bool
    {
        return $this->showVisibility;
    }

    public function setShowVisibility(bool $showVisibility): DisplayConfiguration
    {
        $this->showVisibility = $showVisibility;

        return $this;
    }

    public function isShowActions(): bool
    {
        return $this->showActions;
    }

    public function setShowActions(bool $showActions): DisplayConfiguration
    {
        $this->showActions = $showActions;

        return $this;
    }

    public function isShowNumberOfChildren(): bool
    {
        return $this->showNumberOfChildren;
    }

    public function setShowNumberOfChildren(bool $showNumberOfChildren): DisplayConfiguration
    {
        $this->showNumberOfChildren = $showNumberOfChildren;

        return $this;
    }

    public function isShowNumberOfItems(): bool
    {
        return $this->showNumberOfItems;
    }

    public function setShowNumberOfItems(bool $showNumberOfItems): DisplayConfiguration
    {
        $this->showNumberOfItems = $showNumberOfItems;

        return $this;
    }

    public function isShowItemQuantities(): bool
    {
        return $this->showItemQuantities;
    }

    public function setShowItemQuantities(bool $showItemQuantities): DisplayConfiguration
    {
        $this->showItemQuantities = $showItemQuantities;

        return $this;
    }

    public function getColumns(): ?array
    {
        return $this->columns;
    }

    public function setColumns(?array $columns): DisplayConfiguration
    {
        $this->columns = $columns;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): DisplayConfiguration
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): DisplayConfiguration
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
