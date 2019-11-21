<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\CountersCache;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class CountersExtension
 *
 * @package App\Twig
 */
class CountersExtension extends AbstractExtension
{
    private $countersCache;

    public function __construct(CountersCache $countersCache)
    {
        $this->countersCache = $countersCache;
    }

    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('getCounters', [$this, 'getCounters']),
            new TwigFunction('getTotalItemsCounter', [$this, 'getTotalItemsCounter']),
            new TwigFunction('getTotalChildrenCounter', [$this, 'getTotalChildrenCounter']),
        ];
    }


    public function getCounters($object)
    {
        return $this->countersCache->getCounters($object);
    }

    public function getTotalItemsCounter($objects)
    {
        $counter = 0;

        foreach ($objects as $object) {
            $counter += $this->countersCache->getCounters($object)['items'];
        }

        return $counter;
    }


    public function getTotalChildrenCounter($objects)
    {
        $counter = 0;

        foreach ($objects as $object) {
            $counter += $this->countersCache->getCounters($object)['children'];
        }

        return $counter;
    }


    /**
     * @return string
     */
    public function getName() : string
    {
        return 'counters_extension';
    }
}
