<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\LoggableInterface;
use App\Repository\TemplateRepository;
use App\Validator as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
#[ORM\Table(name: 'koi_template')]
#[ApiResource(
    normalizationContext: ['groups' => ['template:read']],
    denormalizationContext: ['groups' => ['template:write']],
)]
class Template implements BreadcrumbableInterface, LoggableInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['template:read'])]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['template:read', 'template:write'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Field::class, mappedBy: 'template', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => Criteria::ASC])]
    #[ApiSubresource(maxDepth: 1)]
    #[AppAssert\UniqueDatumLabel]
    private DoctrineCollection $fields;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'templates')]
    #[Groups(['template:read'])]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['template:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['template:read'])]
    private ?\DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->fields = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
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

    public function getFields(): DoctrineCollection
    {
        return $this->fields;
    }

    public function addField(Field $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
            $field->setTemplate($this);
        }

        return $this;
    }

    public function removeField(Field $field): self
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
            // set the owning side to null (unless already changed)
            if ($field->getTemplate() === $this) {
                $field->setTemplate(null);
            }
        }

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
}
