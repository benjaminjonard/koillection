<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UrlToImageTransformer implements DataTransformerInterface
{
    public function transform($file): ?string
    {
        return null;
    }

    public function reverseTransform($url): ?UploadedFile
    {
        if (null === $url) {
            return null;
        }

        $content = file_get_contents($url);
        $name = 'scrapped'. uniqid();

        file_put_contents("/tmp/$name", $content);
        $mime = mime_content_type("/tmp/$name");

        return new UploadedFile("/tmp/$name", $name, $mime, null, true);
    }
}
