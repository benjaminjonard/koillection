<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Enum\DatumTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: "koi_field")]
#[ApiResource(
    normalizationContext: ["groups" => ["field:read"]],
    denormalizationContext: ["groups" => ["field:write"]],
)]
class Field
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36, unique: true, options: ["fixed" => true])]
    #[Groups(["field:read"])]
    private string $id;

    #[ORM\Column(type: "string")]
    #[Groups(["field:read", "field:write"])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: "integer")]
    #[Groups(["field:read", "field:write"])]
    private ?int $position = null;

    #[ORM\Column(type: "string")]
    #[Groups(["field:read", "field:write"])]
    #[Assert\Choice(choices: DatumTypeEnum::TYPES)]
    private ?string $type = null;

    #[ORM\ManyToOne(targetEntity: "Template", inversedBy: "fields")]
    #[Assert\NotBlank]
    #[Groups(["field:read", "field:write"])]
    #[ApiSubresource(maxDepth: 1)]
    private ?Template $template = null;

    #[ORM\ManyToOne(targetEntity: "User")]
    #[Groups(["field:read"])]
    private ?User $owner = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
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
}
