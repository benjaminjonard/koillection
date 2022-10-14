<?php

declare(strict_types=1);

namespace App\Tests\Api\Wishlist;

use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Factory\UserFactory;
use App\Factory\WishFactory;
use App\Factory\WishlistFactory;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class WishlistApiTest extends ApiTestCase
{
    use Factories;

    public function test_get_wishlists(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        WishlistFactory::createMany(3, ['owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/wishlists');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wishlist::class);
    }

    public function test_get_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/wishlists/'.$wishlist->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wishlist::class);
        $this->assertJsonContains([
            'id' => $wishlist->getId()
        ]);
    }

    public function test_get_wishlist_children(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        WishlistFactory::createMany(3, ['parent' => $wishlist, 'owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/wishlists/'.$wishlist->getId().'/children');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wishlist::class);
    }

    public function test_get_wishlist_parent(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $parentWishlist = WishlistFactory::createOne(['owner' => $user]);
        $wishlist = WishlistFactory::createOne(['parent' => $parentWishlist, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/wishlists/'.$wishlist->getId().'/parent');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wishlist::class);
        $this->assertJsonContains([
            'id' => $parentWishlist->getId()
        ]);
    }

    public function test_get_wishlist_wishes(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/wishlists/'.$wishlist->getId().'/wishes');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wish::class);
    }

    public function test_post_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/wishlists', ['json' => [
            'name' => 'Books',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wishlist::class);
        $this->assertJsonContains([
            'name' => 'Books',
        ]);
    }

    public function test_put_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['name' => 'Books', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/wishlists/'.$wishlist->getId(), ['json' => [
            'name' => 'Video games',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wishlist::class);
        $this->assertJsonContains([
            'id' => $wishlist->getId(),
            'name' => 'Video games',
        ]);
    }

    public function test_patch_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['name' => 'Books', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/wishlists/'.$wishlist->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'Video games',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wishlist::class);
        $this->assertJsonContains([
            'id' => $wishlist->getId(),
            'name' => 'Video games',
        ]);
    }

    public function test_delete_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/wishlists/'.$wishlist->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
