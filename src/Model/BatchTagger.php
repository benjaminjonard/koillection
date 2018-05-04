<?php

namespace App\Model;

use App\Entity\Collection;

/**
 * Class BatchTagger
 *
 * @package App\Model
 */
class BatchTagger
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var bool
     */
    private $recursive;

    /**
     * BatchTagger constructor.
     */
    public function __construct()
    {
        $this->recursive = false;
        $this->tags = [];
    }

    /**
     * Get collection.
     *
     * @return Collection
     */
    public function getCollection() : Collection
    {
        return $this->collection;
    }

    /**
     * Set collection.
     *
     * @param Collection $collection
     *
     * @return BatchTagger
     */
    public function setCollection(Collection $collection) : BatchTagger
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get tags.
     *
     * @return array
     */
    public function getTags() : array
    {
        return $this->tags;
    }

    /**
     * Set tags.
     *
     * @param array $tags
     *
     * @return BatchTagger
     */
    public function setTags(array $tags) : BatchTagger
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get recursive.
     *
     * @return bool
     */
    public function isRecursive() : bool
    {
        return $this->recursive;
    }

    /**
     * Set recursive.
     *
     * @param bool $recursive
     *
     * @return BatchTagger
     */
    public function setRecursive(bool $recursive) : BatchTagger
    {
        $this->recursive = $recursive;

        return $this;
    }

    public function applyBatch() : int
    {
        return $this->processCollection($this->collection);
    }

    private function processCollection(Collection $collection) : int
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
