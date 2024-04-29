<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\DatumTypeEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'koi_path')]
class Path
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 15)]
    #[Assert\NotBlank]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $path = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    private ?int $position = null;

    #[ORM\ManyToOne(targetEntity: Scraper::class, inversedBy: 'dataPaths')]
    #[Assert\NotBlank]
    private ?Scraper $scraper = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $owner = null;

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Path
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): Path
    {
        $this->path = $path;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): Path
    {
        $this->position = $position;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): Path
    {
        $this->type = $type;

        return $this;
    }

    public function getScraper(): ?Scraper
    {
        return $this->scraper;
    }

    public function setScraper(?Scraper $scraper): Path
    {
        $this->scraper = $scraper;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): Path
    {
        $this->owner = $owner;

        return $this;
    }
}
