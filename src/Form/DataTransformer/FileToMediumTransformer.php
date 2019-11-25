<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Medium;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileToMediumTransformer
 *
 * @package App\Form\DataTransformer
 */
class FileToMediumTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param mixed $image
     *
     * @return array|string
     */
    public function transform($image)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param string $base64
     *
     * @return mixed|null
     */
    public function reverseTransform($file)
    {
        if (!$file) {
            return null;
        }

        $medium = new Medium();
        $medium->setUploadedFile($file);

        return $medium;
    }
}
