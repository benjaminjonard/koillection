<?php

declare(strict_types=1);

namespace App\Tests\Api\Photo;

use App\Factory\AlbumFactory;
use App\Factory\PhotoFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class PhotoApiNotOwnerTest extends ApiTestCase
{
    use Factories;

    public function test_cant_get_another_user_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $owner]);
        $photo = PhotoFactory::createOne(['album' => $album, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/photos/'.$photo->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_photo_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $owner]);
        $photo = PhotoFactory::createOne(['album' => $album, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/photos/'.$photo->getId().'/album');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_post_photo_in_another_user_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/photos/', ['json' => [
            'album' => '/api/albums/'.$album,
            'title' => 'Collection',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_put_another_user_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $owner]);
        $photo = PhotoFactory::createOne(['album' => $album, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/photos/'.$photo->getId(), ['json' => [
            'title' => 'Collection',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $owner]);
        $photo = PhotoFactory::createOne(['album' => $album, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/items/'.$photo->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'Collection',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $owner]);
        $photo = PhotoFactory::createOne(['album' => $album, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/photos/'.$photo->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
