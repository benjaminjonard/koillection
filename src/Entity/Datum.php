<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Attribute\Upload;
use App\Enum\DatumTypeEnum;
use App\Repository\DatumRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DatumRepository::class)]
#[ORM\Table(name: 'koi_datum')]
#[ORM\Index(name: 'idx_datum_label', columns: ['label'])]
#[Assert\Expression('this.getItem() == null or this.getCollection() == null', message: 'error.datum.cant_be_used_by_both_collections_and_items')]
#[Assert\Expression('this.getItem() != null or this.getCollection() != null', message: 'error.datum.must_provide_collection_or_item')]
#[ApiResource(
    operations: [
        new Get(),
        new Put(),
        new Delete(),
        new Patch(),
        new GetCollection(),
        new Post(),
        new Post(uriTemplate: '/data/{id}/image', denormalizationContext: ['groups' => ['datum:image']], inputFormats: ['multipart' => ['multipart/form-data']], openapiContext: ['summary' => 'Upload the Datum image.']),
        new Post(uriTemplate: '/data/{id}/file', denormalizationContext: ['groups' => ['datum:file']], inputFormats: ['multipart' => ['multipart/form-data']], openapiContext: ['summary' => 'Upload the Datum file.'])],
    denormalizationContext: ['groups' => ['datum:write']],
    normalizationContext: ['groups' => ['datum:read']]
)]
#[ApiResource(uriTemplate: '/collections/{id}/data', uriVariables: ['id' => new Link(fromClass: Collection::class, fromProperty: 'data')], normalizationContext: ['groups' => ['datum:read']], operations: [new GetCollection()])]
#[ApiResource(uriTemplate: '/items/{id}/data', uriVariables: ['id' => new Link(fromClass: Item::class, fromProperty: 'data')], normalizationContext: ['groups' => ['datum:read']], operations: [new GetCollection()])]
class Datum implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true, options: ['fixed' => true])]
    #[Groups(['datum:read'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'data')]
    #[Groups(['datum:read', 'datum:write'])]
    private ?Item $item = null;

    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'data')]
    #[Groups(['datum:read', 'datum:write'])]
    private ?Collection $collection = null;

    #[ORM\Column(type: Types::STRING, length: 15)]
    #[Groups(['datum:read', 'datum:write'])]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: DatumTypeEnum::TYPES)]
    private ?string $type = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['datum:read', 'datum:write'])]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['datum:read', 'datum:write'])]
    private ?string $value = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['datum:read', 'datum:write'])]
    private ?int $position = null;

    #[Upload(pathProperty: 'image', smallThumbnailPathProperty: 'imageSmallThumbnail', largeThumbnailPathProperty: 'imageLargeThumbnail')]
    #[Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/avif'], groups: ['datum:image'])]
    #[Groups(['datum:write', 'datum:image'])]
    private ?File $fileImage = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['datum:read'])]
    private ?string $image = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['datum:read'])]
    private ?string $imageSmallThumbnail = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['datum:read'])]
    private ?string $imageLargeThumbnail = null;

    #[Upload(pathProperty: 'file', originalFilenamePathProperty: 'originalFilename')]
    #[Assert\File(groups: ['datum:image'])]
    #[Groups(['datum:write', 'datum:file'])]
    private ?File $fileFile = null;

    #[ORM\Column(type: Types::STRING, nullable: true, unique: true)]
    #[Groups(['datum:read'])]
    private ?string $file = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['datum:read'])]
    private ?string $originalFilename = null;

    #[ORM\ManyToOne(targetEntity: ChoiceList::class)]
    #[Groups(['datum:read', 'datum:write'])]
    #[Assert\When(
        expression: 'this.getType() == "choice-list"',
        constraints: [
            new Assert\NotNull
        ],
    )]
    private ?ChoiceList $choiceList = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['datum:read'])]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['datum:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['datum:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function __toString(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getOrderedListChoices(): array
    {
        if ($this->value === null) {
            return [];
        }

        $selectedChoices = json_decode($this->value, true);
        $orderedSelectedChoices = [];
        foreach ($this->getChoiceList()->getChoices() as $availableChoice) {
            if (\in_array($availableChoice, $selectedChoices)) {
                $orderedSelectedChoices[] = $availableChoice;
            }
        }

        return $orderedSelectedChoices;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

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

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getFileImage(): ?File
    {
        return $this->fileImage;
    }

    public function setFileImage(?File $fileImage): self
    {
        $this->fileImage = $fileImage;
        // Force Doctrine to trigger an update
        if ($fileImage instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTimeImmutable());
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

    public function getImageSmallThumbnail(): ?string
    {
        if ($this->imageSmallThumbnail) {
            return $this->imageSmallThumbnail;
        }

        if ($this->imageLargeThumbnail) {
            return $this->imageLargeThumbnail;
        }

        return $this->image;
    }

    public function setImageSmallThumbnail(?string $imageSmallThumbnail): self
    {
        $this->imageSmallThumbnail = $imageSmallThumbnail;

        return $this;
    }

    public function getImageLargeThumbnail(): ?string
    {
        if (null === $this->imageLargeThumbnail) {
            return $this->image;
        }

        return $this->imageLargeThumbnail;
    }

    public function setImageLargeThumbnail(?string $imageLargeThumbnail): self
    {
        $this->imageLargeThumbnail = $imageLargeThumbnail;

        return $this;
    }

    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    public function setCollection(?Collection $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function getFileFile(): ?File
    {
        return $this->fileFile;
    }

    public function setFileFile(?File $fileFile): Datum
    {
        $this->fileFile = $fileFile;
        // Force Doctrine to trigger an update
        if ($fileFile instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): Datum
    {
        $this->file = $file;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): Datum
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getChoiceList(): ?ChoiceList
    {
        return $this->choiceList;
    }

    public function setChoiceList(?ChoiceList $choiceList): Datum
    {
        $this->choiceList = $choiceList;

        return $this;
    }
}
