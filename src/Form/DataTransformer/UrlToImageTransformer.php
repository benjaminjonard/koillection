<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Service\Scraper\ScrapingUrlGuard;
use App\Service\TemporaryFileManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NoPrivateNetworkHttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UrlToImageTransformer implements DataTransformerInterface
{
    private readonly HttpClientInterface $client;

    public function __construct(
        private readonly ScrapingUrlGuard $urlGuard,
        private readonly TemporaryFileManager $temporaryFileManager,
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

        $path = $this->temporaryFileManager->createFilePath();
        file_put_contents($path, $response->getContent());
        $mime = mime_content_type($path);

        return new UploadedFile($path, $name, $mime, null, true);
    }
}
