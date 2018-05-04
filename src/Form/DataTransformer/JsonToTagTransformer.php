<?php

namespace App\Form\DataTransformer;

use App\Entity\{Tag};
use App\Repository\TagRepository;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class JsonToTagTransformer
 *
 * @package App\Form\DataTransformer
 */
class JsonToTagTransformer implements DataTransformerInterface
{
    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $tags
     *
     * @return array|string
     */
    public function transform($tags)
    {
        $array = [];
        foreach ($tags as $tag) {
            $array[] = $tag->getLabel();
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
        $tags = [];
        foreach (json_decode($json) as $raw) {
            $label = trim($raw);

            if ($label == '') {
                continue;
            }

            $tag = $this->tagRepository->findOneByLabel($label);

            if (!$tag) {
                $tag = new Tag();
                $tag->setLabel($label);
            }

            if (!\in_array($tag, $tags, false)) {
                $tags[] = $tag;
            }
        }

        return $tags;
    }
}
