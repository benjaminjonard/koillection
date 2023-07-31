<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Repository\ScraperRepository;
use App\Validator as AppAssert;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScraperRepository::class)]
#[ORM\Table(name: 'koi_scraper')]
class Scraper implements BreadcrumbableInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $namePath = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[AppAssert\UniqueDatumLabel]
    private ?array $dataPaths = [];

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'scrapers')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
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

    public function setName(?string $name): Scraper
    {
        $this->name = $name;

        return $this;
    }

    public function getNamePath(): ?string
    {
        return $this->namePath;
    }

    public function setNamePath(?string $namePath): Scraper
    {
        $this->namePath = $namePath;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): Scraper
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getDataPaths(): ?array
    {
        return $this->dataPaths;
    }

    public function setDataPaths(?array $dataPaths): Scraper
    {
        usort($dataPaths, fn($a, $b) => $a['position'] - $b['position']);
        $this->dataPaths = $dataPaths;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): Scraper
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): Scraper
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }


    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): Scraper
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
