<?php

declare(strict_types=1);

namespace App\Tests\Api\Wish;

use App\Tests\ApiTestCase;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\PhotoFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\WishFactory;
use App\Tests\Factory\WishlistFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WishApiNotOwnerTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_cant_get_another_user_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/wishes/' . $wish->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_wish_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/wishes/' . $wish->getId() . '/wishlist');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_post_wish_in_another_user_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/wishes/', ['json' => [
            'wishlist' => '/api/wishlists/' . $wishlist,
            'name' => 'Elden Ring',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_put_another_user_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/wishes/' . $wish->getId(), ['json' => [
            'name' => 'Elden Ring',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/wishes/' . $wish->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'Elden Ring',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $album = AlbumFactory::createOne(['owner' => $owner]);
        $photo = PhotoFactory::createOne(['album' => $album, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/photos/' . $photo->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
