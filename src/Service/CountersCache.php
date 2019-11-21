<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\ApcuAdapter;

/**
 * Class CountersCache
 *
 * @package App\Service
 */
class CountersCache
{
    private $cache;

    private $calculator;

    private $contextHandler;

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

        if (!$this->cache->hasItem($context.'_counters')) {
            $counters = [];

            foreach ($this->calculator->computeCounters() as $id => $counter) {
                $counters[$context . '_' . $id] = $counter;
            }

            $cachedCounters = $this->cache->getItem($context.'_counters');
            $cachedCounters->set($counters);
            $this->cache->save($cachedCounters);
        }

        return $this->cache->getItem($context.'_counters')->get()[$key];
    }

    public function reset()
    {
        $this->cache->deleteItems(['default_counters', 'preview_counters', 'user_counters', 'admin_counters']);
    }
}
