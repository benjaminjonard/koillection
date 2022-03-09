<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Collection;

class BatchTagger
{
    private Collection $collection;

    private array $tags;

    private bool $recursive;

    public function __construct()
    {
        $this->recursive = false;
        $this->tags = [];
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }

    public function setCollection(Collection $collection): BatchTagger
    {
        $this->collection = $collection;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): BatchTagger
    {
        $this->tags = $tags;

        return $this;
    }

    public function isRecursive(): bool
    {
        return $this->recursive;
    }

    public function setRecursive(bool $recursive): BatchTagger
    {
        $this->recursive = $recursive;

        return $this;
    }

    public function applyBatch(): int
    {
        return $this->processCollection($this->collection);
    }

    private function processCollection(Collection $collection): int
    {
        $itemCount = 0;
        foreach ($collection->getItems() as $item) {
            $tagAdded = false;
            foreach ($this->getTags() as $tag) {
                if (!$item->hasTag($tag)) {
                    $item->addTag($tag);
                    $tagAdded = true;
                }
            }

            if ($tagAdded) {
                ++$itemCount;
            }
        }

        if ($this->isRecursive()) {
            foreach ($collection->getChildren() as $child) {
                $itemCount += $this->processCollection($child);
            }
        }

        return $itemCount;
    }
}
