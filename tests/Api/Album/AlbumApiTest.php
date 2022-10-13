<?php

declare(strict_types=1);

namespace App\Tests\Api\Album;

use Api\Tests\ApiTestCase;
use App\Entity\Album;
use App\Entity\Photo;
use App\Factory\AlbumFactory;
use App\Factory\PhotoFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class AlbumApiTest extends ApiTestCase
{
    use Factories;

    public function test_get_albums(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        AlbumFactory::createMany(3, ['owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/albums');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Album::class);
    }

    public function test_get_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/albums/'.$album->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Album::class);
        $this->assertJsonContains([
            'id' => $album->getId()
        ]);
    }

    public function test_get_album_children(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);
        AlbumFactory::createMany(3, ['parent' => $album, 'owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/albums/'.$album->getId().'/children');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Album::class);
    }

    public function test_get_album_parent(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $parentAlbum = AlbumFactory::createOne(['owner' => $user]);
        $album = AlbumFactory::createOne(['parent' => $parentAlbum, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/albums/'.$album->getId().'/parent');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Album::class);
        $this->assertJsonContains([
            'id' => $parentAlbum->getId()
        ]);
    }

    public function test_get_album_photos(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $album, 'owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/albums/'.$album->getId().'/photos');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Photo::class);
    }

    public function test_post_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/albums', ['json' => [
            'title' => 'Frieren',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Album::class);
        $this->assertJsonContains([
            'title' => 'Frieren',
        ]);
    }

    public function test_put_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['title' => 'Frieren', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/albums/'.$album->getId(), ['json' => [
            'title' => 'Berserk',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Album::class);
        $this->assertJsonContains([
            'id' => $album->getId(),
            'title' => 'Berserk',
        ]);
    }

    public function test_patch_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['title' => 'Frieren', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/albums/'.$album->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'Berserk',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Album::class);
        $this->assertJsonContains([
            'id' => $album->getId(),
            'title' => 'Berserk',
        ]);
    }

    public function test_delete_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/albums/'.$album->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
