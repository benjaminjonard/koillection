<?php

declare(strict_types=1);

namespace App\Tests\Api\Wish;

use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Tests\ApiTestCase;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\WishFactory;
use App\Tests\Factory\WishlistFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WishApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_get_wishes(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/wishes');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wish::class);
    }

    public function test_get_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/wishes/'.$wish->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wish::class);
        $this->assertJsonContains([
            'id' => $wish->getId()
        ]);
    }

    public function test_get_wish_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/wishes/'.$wish->getId().'/wishlist');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wishlist::class);
        $this->assertJsonContains([
            'id' => $wishlist->getId()
        ]);
    }

    public function test_post_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/wishes', ['json' => [
            'wishlist' => '/api/wishlists/'.$wishlist->getId(),
            'name' => 'Frieren vol. 1',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wish::class);
        $this->assertJsonContains([
            'wishlist' => '/api/wishlists/'.$wishlist->getId(),
            'name' => 'Frieren vol. 1',
        ]);
    }

    public function test_put_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        $wish = WishFactory::createOne(['name' => 'Frieren vol. 1', 'wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/wishes/'.$wish->getId(), ['json' => [
            'wishlist' => '/api/wishlists/'.$wishlist->getId(),
            'name' => 'Frieren vol. 2',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wish::class);
        $this->assertJsonContains([
            'id' => $wish->getId(),
            'wishlist' => '/api/wishlists/'.$wishlist->getId(),
            'name' => 'Frieren vol. 2',
        ]);
    }

    public function test_patch_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        $wish = WishFactory::createOne(['name' => 'Frieren vol. 1', 'wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/wishes/'.$wish->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'Frieren vol. 2',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'id' => $wish->getId(),
            'name' => 'Frieren vol. 2',
        ]);
    }

    public function test_delete_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/wishes/'.$wish->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function test_post_wish_image(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../../assets/fixtures/nyancat.png', "/tmp/{$uniqId}.png");
        $uploadedFile = new UploadedFile("/tmp/{$uniqId}.png", "{$uniqId}.png");
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/wishes/'.$wish->getId().'/image', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wish::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['image']);
    }
}
