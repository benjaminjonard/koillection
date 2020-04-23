<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\ColorPicker;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class ColorListener
 *
 * @package App\EventListener
 */
class ColorListener
{
    /**
     * @var ColorPicker
     */
    private $colorPicker;

    /**
     * CollectionListener constructor.
     * @param ColorPicker $colorPicker
     */
    public function __construct(ColorPicker $colorPicker)
    {
        $this->colorPicker = $colorPicker;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (true === property_exists($entity, 'color')) {
            $entity->setColor($this->colorPicker->pickRandomColor());
        }
    }
}
