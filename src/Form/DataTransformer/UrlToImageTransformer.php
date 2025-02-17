<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpClient\CurlHttpClient;

class UrlToImageTransformer implements DataTransformerInterface
{
    private readonly ?CurlHttpClient $client;

    public function __construct() {
        $this->client = new CurlHttpClient();
    }

    #[\Override]
    public function transform($file): ?string
    {
        return null;
    }

    #[\Override]
    public function reverseTransform($url): ?UploadedFile
    {
        if (null === $url) {
            return null;
        }

        if (str_starts_with($url, '//')) {
            $url = 'https:' . $url;
        }

        $response = $this->client->request(
                'GET',
                $url,
                ['timeout' => 2.5]
        );
        $name = 'scraped' . uniqid();

        file_put_contents("/tmp/{$name}", $response->getContent());
        $mime = mime_content_type("/tmp/{$name}");

        return new UploadedFile("/tmp/{$name}", $name, $mime, null, true);
    }
}
