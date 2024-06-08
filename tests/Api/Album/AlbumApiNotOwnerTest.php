<?php

declare(strict_types=1);

namespace App\Tests\Api\Album;

use App\Entity\Album;
use App\Entity\Photo;
use App\Tests\ApiTestCase;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\PhotoFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AlbumApiNotOwnerTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_cant_get_another_user_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $album = AlbumFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/albums/' . $album->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_album_children(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $album = AlbumFactory::createOne(['owner' => $owner]);
        AlbumFactory::createMany(3, ['parent' => $album, 'owner' => $owner]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/albums/' . $album->getId() . '/children');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Album::class);
    }

    public function test_cant_get_another_user_album_parent(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $parent = AlbumFactory::createOne(['owner' => $owner]);
        $album = AlbumFactory::createOne(['parent' => $parent, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/albums/' . $album->getId() . '/parent');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_album_photos(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $album = AlbumFactory::createOne(['owner' => $owner]);
        PhotoFactory::createMany(3, ['album' => $album, 'owner' => $owner]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/albums/' . $album->getId() . '/photos');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Photo::class);
    }

    public function test_cant_post_album_in_another_user_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $album = AlbumFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/albums/', ['json' => [
            'parent' => '/api/albums/' . $album->getId()
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_put_another_user_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $album = AlbumFactory::createOne(['title' => 'Frieren', 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/albums/' . $album->getId(), ['json' => [
            'title' => 'Berserk',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $album = AlbumFactory::createOne(['title' => 'Frieren', 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/albums/' . $album->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'Berserk',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $album = AlbumFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/albums/' . $album->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
