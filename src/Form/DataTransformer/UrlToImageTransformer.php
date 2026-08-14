<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Service\Scraper\ScrapingUrlGuard;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NoPrivateNetworkHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UrlToImageTransformer implements DataTransformerInterface
{
    private readonly HttpClientInterface $client;

    public function __construct(
        private readonly ScrapingUrlGuard $urlGuard,
    ) {
        $this->client = new NoPrivateNetworkHttpClient(new CurlHttpClient());
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

        $this->urlGuard->assertSchemeIsSupported($url);

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
