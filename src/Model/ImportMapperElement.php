<?php

declare(strict_types=1);

namespace App\Model;

use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ImportMapperElement
{
    #[Assert\NotBlank]
    private int $columnIndex;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: DatumTypeEnum::TYPES)]
    private string $datumType;

    #[Assert\NotBlank]
    private string $datumLabel;

    #[Assert\NotBlank]
    private int $datumPosition = 0;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: VisibilityEnum::VISIBILITIES)]
    private string $datumVisibility;

    private bool $createCorrespondingTags = false;

    public function getColumnIndex(): int
    {
        return $this->columnIndex;
    }

    public function setColumnIndex(int $columnIndex): ImportMapperElement
    {
        $this->columnIndex = $columnIndex;

        return $this;
    }

    public function getDatumType(): string
    {
        return $this->datumType;
    }

    public function setDatumType(string $datumType): ImportMapperElement
    {
        $this->datumType = $datumType;

        return $this;
    }

    public function getDatumLabel(): string
    {
        return $this->datumLabel;
    }

    public function setDatumLabel(string $datumLabel): ImportMapperElement
    {
        $this->datumLabel = $datumLabel;

        return $this;
    }

    public function getDatumVisibility(): string
    {
        return $this->datumVisibility;
    }

    public function setDatumVisibility(string $datumVisibility): ImportMapperElement
    {
        $this->datumVisibility = $datumVisibility;

        return $this;
    }

    public function getCreateCorrespondingTags(): bool
    {
        return $this->createCorrespondingTags;
    }

    public function setCreateCorrespondingTags(bool $createCorrespondingTags): ImportMapperElement
    {
        $this->createCorrespondingTags = $createCorrespondingTags;

        return $this;
    }

    public function getDatumPosition(): int
    {
        return $this->datumPosition;
    }

    public function setDatumPosition(int $datumPosition): ImportMapperElement
    {
        $this->datumPosition = $datumPosition;

        return $this;
    }
}
