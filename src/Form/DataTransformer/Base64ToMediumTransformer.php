<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Medium;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Base64ToMediumTransformer
 *
 * @package App\Form\DataTransformer
 */
class Base64ToMediumTransformer implements DataTransformerInterface
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
     * @param mixed $base64
     * @return Medium|mixed
     * @throws \Exception
     */
    public function reverseTransform($base64)
    {
        if (null === $base64) {
            return null;
        }

        preg_match('#data:(image/([\w]+));base64,(.*)#', $base64, $matches);
        $data = base64_decode($matches[3]);
        $name = uniqid('col_').'.'.$matches[2];
        if (!file_exists('tmp/')) {
            if (!mkdir('tmp/', 0777, true)) {
                throw new \Exception('There was a problem while uploading the image. Please try again!');
            }
        }
        $path = 'tmp/'.$name;
        file_put_contents($path, $data);
        $file = new UploadedFile($path, $name, $matches[1], null, true);
        $medium = new Medium();
        $medium->setUploadedFile($file);

        return $medium;
    }
}
