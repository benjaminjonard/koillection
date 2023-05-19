<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\Item;
use App\Tests\ApiTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UploadTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_upload_png(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../assets/fixtures/nyancat.png', "/tmp/{$uniqId}.png");
        $uploadedFile = new UploadedFile("/tmp/{$uniqId}.png", "{$uniqId}.png");
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/items/'.$item->getId().'/image', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Item::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['image']);
    }

    public function test_upload_jpg(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../assets/fixtures/nyancat.jpg', "/tmp/{$uniqId}.jpg");
        $uploadedFile = new UploadedFile("/tmp/{$uniqId}.jpg", "{$uniqId}.jpg");
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/items/'.$item->getId().'/image', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Item::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['image']);
    }

    public function test_upload_webp(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../assets/fixtures/nyancat.webp', "/tmp/{$uniqId}.webp");
        $uploadedFile = new UploadedFile("/tmp/{$uniqId}.webp", "{$uniqId}.webp");
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/items/'.$item->getId().'/image', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Item::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['image']);
    }

    public function test_upload_gif(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../assets/fixtures/nyancat.gif', "/tmp/{$uniqId}.gif");
        $uploadedFile = new UploadedFile("/tmp/{$uniqId}.gif", "{$uniqId}.gif");
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/items/'.$item->getId().'/image', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Item::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['image']);
    }

    public function test_upload_avif(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../assets/fixtures/nyancat.avif', "/tmp/{$uniqId}.avif");
        $uploadedFile = new UploadedFile("/tmp/{$uniqId}.avif", "{$uniqId}.avif");
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/items/'.$item->getId().'/image', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Item::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['image']);
    }
}
