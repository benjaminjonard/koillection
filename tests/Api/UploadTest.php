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

    public function test_upload_avif(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $uploadedFile = $this->createFile('avif');

        // Act
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
        $this->assertFileExists(json_decode($crawler->getContent(), true)['image']);
    }

    public function test_upload_png(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $uploadedFile = $this->createFile('png');

        // Act
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
        $this->assertFileExists(json_decode($crawler->getContent(), true)['image']);
    }

    public function test_upload_jpeg(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $uploadedFile = $this->createFile('jpeg');

        // Act
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
        $this->assertFileExists(json_decode($crawler->getContent(), true)['image']);
    }

    public function test_upload_webp(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $uploadedFile = $this->createFile('webp');

        // Act
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
        $this->assertFileExists(json_decode($crawler->getContent(), true)['image']);
    }
}
