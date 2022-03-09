<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Repository\ItemRepository;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\DataTransformerInterface;

class JsonToItemTransformer implements DataTransformerInterface
{
    public function __construct(
        private ItemRepository $itemRepository,
        private Packages $assetManager
    ) {
    }

    public function transform($items): mixed
    {
        $array = [];
        foreach ($items as $item) {
            $array[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'thumbnail' => $item->getImageSmallThumbnail() ? $this->assetManager->getUrl($item->getImageSmallThumbnail()) : null,
            ];
        }

        return json_encode($array);
    }

    public function reverseTransform($json): mixed
    {
        $items = [];
        foreach (json_decode($json, true) as $id) {
            $item = $this->itemRepository->find($id);

            if (!\in_array($item, $items, false)) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
