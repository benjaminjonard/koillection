<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ImportStatusEnum;
use App\Repository\ImportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ImportRepository::class)]
#[ORM\Table(name: 'koi_import')]
class Import
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'imports')]
    private ?Collection $collection = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'imports')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::STRING)]
    private string $filename;

    #[ORM\Column(type: Types::STRING)]
    private string $status = ImportStatusEnum::PENDING;

    #[ORM\Column(type: Types::INTEGER)]
    private int $totalRows = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $processedRows = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $importedItems = 0;

    #[ORM\Column(type: Types::JSON)]
    private array $errors = [];

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $finishedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    public function setCollection(?Collection $collection): Import
    {
        $this->collection = $collection;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): Import
    {
        $this->owner = $owner;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): Import
    {
        $this->filename = $filename;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Import
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function setTotalRows(int $totalRows): Import
    {
        $this->totalRows = $totalRows;

        return $this;
    }

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    public function setProcessedRows(int $processedRows): Import
    {
        $this->processedRows = $processedRows;

        return $this;
    }

    public function getImportedItems(): int
    {
        return $this->importedItems;
    }

    public function setImportedItems(int $importedItems): Import
    {
        $this->importedItems = $importedItems;

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): Import
    {
        $this->errors = $errors;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): Import
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeImmutable $finishedAt): Import
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }
}
