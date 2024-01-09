<?php

declare(strict_types=1);

namespace App\Tests\Api\Wishlist;

use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Tests\ApiTestCase;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\WishFactory;
use App\Tests\Factory\WishlistFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WishlistApiNotOwnerTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_cant_get_another_user_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/wishlists/' . $wishlist->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_wishlist_children(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);
        WishlistFactory::createMany(3, ['parent' => $wishlist, 'owner' => $owner]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/wishlists/' . $wishlist->getId() . '/children');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wishlist::class);
    }

    public function test_cant_get_another_user_wishlist_parent(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $parent = WishlistFactory::createOne(['owner' => $owner]);
        $wishlist = WishlistFactory::createOne(['parent' => $parent, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/wishlists/' . $wishlist->getId() . '/parent');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_wishlist_wishes(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);
        WishFactory::createMany(3, ['wishlist' => $wishlist, 'owner' => $owner]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/wishlists/' . $wishlist->getId() . '/wishes');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wish::class);
    }

    public function test_cant_post_wishlist_in_another_user_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/wishlists/', ['json' => [
            'parent' => '/api/wishlist/' . $wishlist->getId()
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_put_another_user_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/wishlists/' . $wishlist->getId(), ['json' => [
            'name' => 'Video game',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/wishlists/' . $wishlist->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'Video game',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/wishlists/' . $wishlist->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
