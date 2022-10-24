<?php

declare(strict_types=1);

namespace App\Tests\Api\Photo;

use App\Entity\Album;
use App\Entity\Photo;
use App\Tests\ApiTestCase;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\PhotoFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PhotoApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_get_photos(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $album, 'owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/photos');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Photo::class);
    }

    public function test_get_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);
        $photo = PhotoFactory::createOne(['album' => $album, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/photos/'.$photo->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Photo::class);
        $this->assertJsonContains([
            'id' => $photo->getId()
        ]);
    }

    public function test_get_photo_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);
        $photo = PhotoFactory::createOne(['album' => $album, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/photos/'.$photo->getId().'/album');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Album::class);
        $this->assertJsonContains([
            'id' => $album->getId()
        ]);
    }

    public function test_post_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/photos', ['json' => [
            'album' => '/api/albums/'.$album->getId(),
            'title' => 'Home collection',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Photo::class);
        $this->assertJsonContains([
            'album' => '/api/albums/'.$album->getId(),
            'title' => 'Home collection',
        ]);
    }

    public function test_put_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);
        $photo = PhotoFactory::createOne(['title' => 'Home collection', 'album' => $album, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/photos/'.$photo->getId(), ['json' => [
            'title' => 'Other collection',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Photo::class);
        $this->assertJsonContains([
            'id' => $photo->getId(),
            'title' => 'Other collection',
        ]);
    }

    public function test_patch_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);
        $photo = PhotoFactory::createOne(['title' => 'Home collection', 'album' => $album, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/photos/'.$photo->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'Other collection',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'id' => $photo->getId(),
            'title' => 'Other collection',
        ]);
    }

    public function test_delete_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);
        $photo = PhotoFactory::createOne(['album' => $album, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/photos/'.$photo->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function test_post_photo_image(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);
        $photo = PhotoFactory::createOne(['album' => $album, 'owner' => $user]);

        // Act
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../../assets/fixtures/nyancat.png', "/tmp/$uniqId.png");
        $uploadedFile = new UploadedFile("/tmp/$uniqId.png", "$uniqId.png");
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/photos/'.$photo->getId().'/image', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Photo::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['image']);
    }
}
