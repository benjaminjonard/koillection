<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'koi_field')]
#[ApiResource(
    denormalizationContext: ['groups' => ['field:write']],
    normalizationContext: ['groups' => ['field:read']]
)]
#[ApiResource(uriTemplate: '/templates/{id}/fields', uriVariables: ['id' => new Link(fromClass: Template::class, fromProperty: 'fields')], normalizationContext: ['groups' => ['field:read']], operations: [new GetCollection()])]
class Field
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['field:read'])]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['field:read', 'field:write'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['field:read', 'field:write'])]
    #[Assert\NotBlank]
    private ?int $position = null;

    #[ORM\Column(type: Types::STRING, length: 15)]
    #[Groups(['field:read', 'field:write'])]
    #[Assert\Choice(choices: DatumTypeEnum::TYPES)]
    #[Assert\NotBlank]
    private ?string $type = null;

    #[ORM\ManyToOne(targetEntity: ChoiceList::class)]
    #[Groups(['field:read', 'field:write'])]
    #[Assert\When(
        expression: 'this.getType() == "choice-list"',
        constraints: [
            new Assert\NotNull()
        ],
    )]
    private ?ChoiceList $choiceList = null;

    #[ORM\ManyToOne(targetEntity: Template::class, inversedBy: 'fields')]
    #[Assert\NotBlank]
    #[Groups(['field:read', 'field:write'])]
    private ?Template $template = null;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['field:read', 'field:write'])]
    #[Assert\Choice(choices: VisibilityEnum::VISIBILITIES)]
    private string $visibility = VisibilityEnum::VISIBILITY_PUBLIC;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['field:read'])]
    private ?User $owner = null;

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122();
    }

    public function getTypeLabel(): string
    {
        return DatumTypeEnum::getTypeLabel($this->type);
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): Field
    {
        $this->owner = $owner;

        return $this;
    }

    public function getChoiceList(): ?ChoiceList
    {
        return $this->choiceList;
    }

    public function setChoiceList(?ChoiceList $choiceList): Field
    {
        $this->choiceList = $choiceList;

        return $this;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): Field
    {
        $this->visibility = $visibility;

        return $this;
    }
}
