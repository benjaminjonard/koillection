<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Attribute\Upload;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Interfaces\LoggableInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'koi_import')]
class Import implements BreadcrumbableInterface, LoggableInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    private string $id;

    #[Upload(pathProperty: 'file', originalFilenamePathProperty: 'filename')]
    #[Assert\File(mimeTypes: ['text/csv'])]
    private ?File $fileFile = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    private ?string $file = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $filename = null;

    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'imports')]
    private ?Collection $collection = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'imports')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::STRING)]
    private string $status;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $nameIndex;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $imageIndex;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $mapping = [];

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $totalNumberOfItems;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $numberOfImportedItems;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $numberOfDuplicatedItems;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $numberOfSkippedItems;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getId() ?? '';
    }

    public function getHeaders(): false|array
    {
        $file = new File($this->file);

        $headers = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $headers = fgetcsv($handle, 0, ',');

            fclose($handle);
        }

        return $headers;
    }

    public function getRows(): false|array
    {
        $file = new File($this->file);

        $data = [];
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $headers = fgetcsv($handle, 0, ',');

            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $data[] = $row;
            }

            fclose($handle);
        }

        return $data;
    }

    public function getId(): ?string
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Import
    {
        $this->status = $status;

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

    public function getFileFile(): ?File
    {
        return $this->fileFile;
    }

    public function setFileFile(?File $fileFile): Import
    {
        $this->fileFile = $fileFile;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): Import
    {
        $this->file = $file;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): Import
    {
        $this->filename = $filename;

        return $this;
    }

    public function getMapping(): ?array
    {
        $mapping = $this->mapping;
        usort($mapping, function ($a, $b) {
            return $a['datumPosition'] <=> $b['datumPosition'];
        });

        return $mapping;
    }

    public function setMapping(?array $mapping): Import
    {
        $this->mapping = $mapping;

        return $this;
    }

    public function getTotalNumberOfItems(): ?int
    {
        return $this->totalNumberOfItems;
    }

    public function setTotalNumberOfItems(?int $totalNumberOfItems): Import
    {
        $this->totalNumberOfItems = $totalNumberOfItems;

        return $this;
    }

    public function getNumberOfImportedItems(): ?int
    {
        return $this->numberOfImportedItems;
    }

    public function setNumberOfImportedItems(?int $numberOfImportedItems): Import
    {
        $this->numberOfImportedItems = $numberOfImportedItems;

        return $this;
    }

    public function getNumberOfDuplicatedItems(): ?int
    {
        return $this->numberOfDuplicatedItems;
    }

    public function setNumberOfDuplicatedItems(?int $numberOfDuplicatedItems): Import
    {
        $this->numberOfDuplicatedItems = $numberOfDuplicatedItems;

        return $this;
    }

    public function getNumberOfSkippedItems(): ?int
    {
        return $this->numberOfSkippedItems;
    }

    public function setNumberOfSkippedItems(?int $numberOfSkippedItems): Import
    {
        $this->numberOfSkippedItems = $numberOfSkippedItems;

        return $this;
    }

    public function getNameIndex(): ?int
    {
        return $this->nameIndex;
    }

    public function setNameIndex(?int $nameIndex): Import
    {
        $this->nameIndex = $nameIndex;

        return $this;
    }

    public function getImageIndex(): ?int
    {
        return $this->imageIndex;
    }

    public function setImageIndex(?int $imageIndex): Import
    {
        $this->imageIndex = $imageIndex;

        return $this;
    }
}
