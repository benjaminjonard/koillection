<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Interfaces\LoggableInterface;
use App\Repository\ChoiceListRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ChoiceListRepository::class)]
#[ORM\Table(name: 'koi_choice_list')]
#[ApiResource(
    normalizationContext: ['groups' => ['choiceList:read']],
    denormalizationContext: ['groups' => ['choiceList:write']],
)]
class ChoiceList implements LoggableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['choiceList:read'])]
    private string $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['choiceList:read', 'choiceList:write'])]
    private string $name;

    #[ORM\Column(type: Types::ARRAY)]
    #[Groups(['choiceList:read', 'choiceList:write'])]
    private array $choices;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['choiceList:read'])]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['choiceList:read'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['choiceList:read'])]
    private ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->values = [];
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ChoiceList
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): ChoiceList
    {
        $this->type = $type;

        return $this;
    }

    public function getChoices(): array
    {
        return $this->choices;
    }

    public function setChoices(array $choices): ChoiceList
    {
        $this->choices = $choices;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): ChoiceList
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): ChoiceList
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): ChoiceList
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
