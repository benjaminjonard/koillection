<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class Import
{
    private string $id;

    private ?string $collectionId = null;

    private ?Collection $collection = null;

    #[Assert\File(mimeTypes: ['text/csv', 'text/plain'])]
    private ?File $fileFile = null;

    private ?string $path = null;

    #[Assert\NotNull(groups: ['import:mapping'])]
    private ?int $nameIndex = null;

    private ?int $imageIndex = null;

    private ?int $quantityIndex = null;

    private ?int $tagsIndex = null;

    private ?int $visibilityIndex = null;

    private array $mapping = [];

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122();
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'collectionId' => $this->collection?->getId() ?? $this->collectionId,
            'path' => $this->path,
            'nameIndex' => $this->nameIndex,
            'imageIndex' => $this->imageIndex,
            'quantityIndex' => $this->quantityIndex,
            'tagsIndex' => $this->tagsIndex,
            'visibilityIndex' => $this->visibilityIndex,
            'mapping' => $this->mapping,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->collectionId = $data['collectionId'];
        $this->path = $data['path'];
        $this->nameIndex = $data['nameIndex'];
        $this->imageIndex = $data['imageIndex'];
        $this->quantityIndex = $data['quantityIndex'];
        $this->tagsIndex = $data['tagsIndex'];
        $this->visibilityIndex = $data['visibilityIndex'];
        $this->mapping = $data['mapping'];
    }

    public function getHeaders(): false|array
    {
        $headers = [];

        if (($handle = fopen($this->path, 'r')) !== false) {
            $this->skipBom($handle);
            $headers = fgetcsv($handle, 0, ',', '"', '');

            fclose($handle);
        }

        return $headers;
    }

    public function getRows(): false|array
    {
        $data = [];
        if (($handle = fopen($this->path, 'r')) !== false) {
            $this->skipBom($handle);
            fgetcsv($handle, 0, ',', '"', '');

            while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                $data[] = $row;
            }

            fclose($handle);
        }

        return $data;
    }

    private function skipBom($handle): void
    {
        if (fread($handle, 3) !== "\xEF\xBB\xBF") {
            rewind($handle);
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCollectionId(): ?string
    {
        return $this->collectionId;
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

    public function getFileFile(): ?File
    {
        return $this->fileFile;
    }

    public function setFileFile(?File $fileFile): Import
    {
        $this->fileFile = $fileFile;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): Import
    {
        $this->path = $path;

        return $this;
    }

    public function getMapping(): array
    {
        $mapping = $this->mapping;
        usort($mapping, function ($a, $b) {
            return $a['datumPosition'] <=> $b['datumPosition'];
        });

        return $mapping;
    }

    public function setMapping(?array $mapping): Import
    {
        $this->mapping = $mapping ?? [];

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

    public function getQuantityIndex(): ?int
    {
        return $this->quantityIndex;
    }

    public function setQuantityIndex(?int $quantityIndex): Import
    {
        $this->quantityIndex = $quantityIndex;

        return $this;
    }

    public function getTagsIndex(): ?int
    {
        return $this->tagsIndex;
    }

    public function setTagsIndex(?int $tagsIndex): Import
    {
        $this->tagsIndex = $tagsIndex;

        return $this;
    }

    public function getVisibilityIndex(): ?int
    {
        return $this->visibilityIndex;
    }

    public function setVisibilityIndex(?int $visibilityIndex): Import
    {
        $this->visibilityIndex = $visibilityIndex;

        return $this;
    }
}
