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
    private TagAwareAdapter $cache;

    private CounterCalculator $calculator;

    private ContextHandler $contextHandler;

    private Security $security;

    public function __construct(CounterCalculator $calculator, ContextHandler $contextHandler, Security $security)
    {
        $this->cache = new TagAwareAdapter(new ApcuAdapter());
        $this->calculator = $calculator;
        $this->contextHandler = $contextHandler;
        $this->security = $security;
    }

    public function getCounters($element): array
    {
        $context = $this->contextHandler->getContext();
        $contextUserId = $this->contextHandler->getContextUser()->getId();

        $key = '';
        if ($context === 'user') {
            $key .= $this->security->getUser() instanceof User ? 'authenticated_' : 'anonymous_';
        }

        $cacheKey = $contextUserId. '_' . $context;
        $counters = $this->cache->get($cacheKey, function (ItemInterface $item) use ($key, $contextUserId) {
            $counters = [];

            foreach ($this->calculator->computeCounters() as $id => $counter) {
                $counters[$key . $id] = $counter;
            }

            $item->tag($contextUserId);

            return $counters;
        });

        if (is_object($element)) {
            $key .= $element->getId();
        } else {
            $key .= $element;
        }

        return $counters[$key];
    }

    public function clear()
    {
        $this->cache->clear();
    }

    public function invalidateCurrentUser()
    {
        $this->cache->invalidateTags([$this->security->getUser()->getId()]);
    }
}
