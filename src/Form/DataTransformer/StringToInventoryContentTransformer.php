<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Collection;
use App\Service\InventoryHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class StringToInventoryContentTransformer
 *
 * @package App\Form\DataTransformer
 */
class StringToInventoryContentTransformer implements DataTransformerInterface
{
    /**
     * @var InventoryHandler
     */
    protected $inventoryHandler;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * StringToInventoryContentTransformer constructor.
     * @param InventoryHandler $inventoryHandler
     */
    public function __construct(InventoryHandler $inventoryHandler, EntityManagerInterface $em)
    {
        $this->inventoryHandler = $inventoryHandler;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $tags
     *
     * @return array|string
     */
    public function transform($content)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @param string $string
     *
     * @return mixed|null
     */
    public function reverseTransform($string)
    {
        if ($string === null) {
            return json_encode([]);
        }

        $ids = explode(',', $string);
        $collections = $this->em->getRepository(Collection::class)->findAllWithItems();
        $content = $this->inventoryHandler->buildInventory($collections, $ids);

        return json_encode($content);
    }
}
