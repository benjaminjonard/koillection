<?php

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class JsonToTagTransformer
 *
 * @package App\Form\DataTransformer
 */
class JsonToTagTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * JsonToMunicipalitiesTransformer constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
        $repo = $this->em->getRepository(Tag::class);
        $tags = [];
        foreach (json_decode($json) as $raw) {
            $label = trim($raw);

            if ($label == '') {
                continue;
            }

            $tag = $repo->findOneByLabel($label);

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
