<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\ItemInterface;

class CountersCache
{
    private readonly TagAwareAdapter $cache;

    public function __construct(
        private readonly CounterCalculator $calculator,
        private readonly ContextHandler $contextHandler,
        private readonly Security $security
    ) {
        $this->cache = new TagAwareAdapter(new ApcuAdapter());
    }

    public function getCounters($element): array
    {
        $context = $this->contextHandler->getContext();
        $contextUserId = $this->contextHandler->getContextUser()->getId();

        $key = '';
        $cacheKey = $contextUserId.'_'.$context;
        if ('shared' === $context) {
            $key .= $this->security->getUser() instanceof User ? 'authenticated_' : 'anonymous_';
            $cacheKey .= $this->security->getUser() instanceof User ? '_authenticated' : '_anonymous';
        }

        $counters = $this->cache->get($cacheKey, function (ItemInterface $item) use ($key, $contextUserId): array {
            $counters = [];
            foreach ($this->calculator->computeCounters() as $id => $counter) {
                $counters[$key.$id] = $counter;
            }

            $item->tag($contextUserId);

            return $counters;
        });

        if (\is_object($element)) {
            $key .= $element->getId();
        } else {
            $key .= $element;
        }

        return $counters[$key];
    }

    public function clear(): void
    {
        $this->cache->clear();
    }

    public function invalidateCurrentUser(): void
    {
        if ($this->security->getUser() !== null) {
            $this->cache->invalidateTags([$this->security->getUser()->getId()]);
        }
    }
}
