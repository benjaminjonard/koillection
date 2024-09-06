<?php

declare(strict_types=1);

namespace App\Model\Search;

use App\Enum\DisplayModeEnum;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Search
{
    #[Assert\Length(min: 2, minMessage: 'error.search.too_short')]
    private ?string $term = null;

    private ?\DateTimeImmutable $createdAt = null;

    private bool $searchInData = false;

    private string $displayMode = DisplayModeEnum::DISPLAY_MODE_GRID;

    public function getTerm(): ?string
    {
        return $this->term;
    }

    public function setTerm(string $term): Search
    {
        $this->term = $term;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): Search
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSearchInData(): bool
    {
        return $this->searchInData;
    }

    public function setSearchInData(bool $searchInData): Search
    {
        $this->searchInData = $searchInData;

        return $this;
    }

    public function getDisplayMode(): string
    {
        return $this->displayMode;
    }

    public function setDisplayMode(?string $displayMode): Search
    {
        if ($displayMode === null) {
            return $this;
        }

        $this->displayMode = $displayMode;

        return $this;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (null === $this->getTerm() && !$this->getCreatedAt() instanceof \DateTimeImmutable) {
            $context
                ->buildViolation('error.search.empty')
                ->addViolation()
            ;
        }
    }
}
