<?php

declare(strict_types=1);

namespace App\Model\Search;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Search
{
    #[Assert\Length(min: 2, minMessage: 'error.search.too_short')]
    private ?string $term = null;

    private ?\DateTimeImmutable $createdAt = null;

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

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (null === $this->getTerm() && null === $this->getCreatedAt()) {
            $context
                ->buildViolation('error.search.empty')
                ->addViolation()
            ;
        }
    }
}
