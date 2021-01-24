<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\DataTransformerInterface;

class JsonToItemTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var Packages
     */
    private Packages $assetManager;

    /**
     * JsonToTagTransformer constructor.
     * @param EntityManagerInterface $em
     * @param Packages $assetManager
     */
    public function __construct(EntityManagerInterface $em, Packages $assetManager)
    {
        $this->em = $em;
        $this->assetManager = $assetManager;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $items
     *
     * @return array|string
     */
    public function transform($items)
    {
        $array = [];
        foreach ($items as $item) {
            $array[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'thumbnail' => $this->assetManager->getUrl($item->getImageSmallThumbnail()),
            ];
        }

        return json_encode($array);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $string
     *
     * @return mixed|null
     */
    public function reverseTransform($json)
    {
        $repo = $this->em->getRepository(Item::class);
        $items = [];
        foreach (json_decode($json, true) as $id) {
            $item = $repo->find($id);

            if (!\in_array($item, $items, false)) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
