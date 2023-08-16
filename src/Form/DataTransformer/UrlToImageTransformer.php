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

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $content = curl_exec($ch);
        curl_close($ch);

        $name = 'scraped' . uniqid();

        file_put_contents("/tmp/$name", $content);
        $mime = mime_content_type("/tmp/$name");

        return new UploadedFile("/tmp/$name", $name, $mime, null, true);
    }
}
