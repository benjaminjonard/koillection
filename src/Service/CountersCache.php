<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class CountersCache
{
    private ApcuAdapter $cache;

    private CounterCalculator $calculator;

    private ContextHandler $contextHandler;

    public function __construct(CounterCalculator $calculator, ContextHandler $contextHandler)
    {
        $this->cache = new ApcuAdapter();
        $this->calculator = $calculator;
        $this->contextHandler = $contextHandler;
    }

    public function getCounters($object)
    {
        $context = $this->contextHandler->getContext();
        $key = $context . '_' . $object->getId();

        $counters = $this->cache->get($context.'_counters', function (ItemInterface $item) use ($context) {
            $counters = [];
            foreach ($this->calculator->computeCounters() as $id => $counter) {
                $counters[$context . '_' . $id] = $counter;
            }

            return $counters;
        });

        return $counters[$key];
    }

    public function reset()
    {
        $this->cache->deleteItems(['default_counters', 'preview_counters', 'user_counters', 'admin_counters']);
    }
}
