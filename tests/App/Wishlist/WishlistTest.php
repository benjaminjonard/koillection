<?php

declare(strict_types=1);

namespace App\Tests\App\Wishlist;

use App\Enum\DisplayModeEnum;
use App\Enum\VisibilityEnum;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\WishFactory;
use App\Tests\Factory\WishlistFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Tests\AppTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WishlistTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_get_wishlist_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        WishlistFactory::createMany(3, ['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/wishlists');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Wishlists', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
    }

    public function test_can_edit_wishlist_index(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        WishlistFactory::createMany(3, ['owner' => $user]);

        // Act
        $this->client->request('GET', '/wishlists/edit');
        $crawler = $this->client->submitForm('Submit', [
            'display_configuration[displayMode]' => DisplayModeEnum::DISPLAY_MODE_LIST,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Wishlists', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.list-element'));
    }

    public function test_can_get_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $wishlist = WishlistFactory::createOne(['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/wishlists/'.$wishlist->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals($wishlist->getName(), $crawler->filter('h1')->text());
    }

    public function test_can_post_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $parent = WishlistFactory::createOne(['owner' => $user]);

        // Act
        $this->client->request('GET', '/wishlists/add?parent='.$parent->getId());

        $crawler = $this->client->submitForm('Submit', [
            'wishlist[name]' => 'Books',
            'wishlist[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC
        ]);

        // Assert
        $this->assertSame('Books', $crawler->filter('h1')->text());
    }

    public function test_can_edit_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        $filesystem = new Filesystem();
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../../assets/fixtures/nyancat.png', "/tmp/{$uniqId}.png");

        $wishlist = WishlistFactory::createOne(['owner' => $user, 'image' => "/tmp/{$uniqId}.png"]);

        // Act
        $this->client->request('GET', '/wishlists/'.$wishlist->getId().'/edit');
        $crawler = $this->client->submitForm('Submit', [
            'wishlist[name]' => 'Video games',
            'wishlist[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC
        ]);

        // Assert
        $this->assertSame('Video games', $crawler->filter('h1')->text());
        $this->assertFileExists("/tmp/{$uniqId}.png");
    }

    public function test_can_delete_wishlist_image(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        $filesystem = new Filesystem();
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../../assets/fixtures/nyancat.png', "/tmp/{$uniqId}.png");
        $album = WishlistFactory::createOne(['name' => 'Books', 'owner' => $user, 'image' => "/tmp/{$uniqId}.png"]);

        // Act
        $this->client->request('GET', '/wishlists/'.$album->getId().'/edit');
        $crawler = $this->client->submitForm('Submit', [
            'wishlist[deleteImage]' => true,
        ]);

        // Assert
        $this->assertSame('B', $crawler->filter('.collection-header')->filter('.thumbnail')->text());
        $this->assertFileDoesNotExist("/tmp/{$uniqId}.png");
    }

    public function test_can_delete_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        $childWishlist = WishlistFactory::createOne(['parent' => $wishlist, 'owner' => $user]);
        $otherWishlist = WishlistFactory::createOne(['owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlist, 'owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $childWishlist, 'owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $otherWishlist, 'owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/wishlists/'.$wishlist->getId());
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/wishlists/'.$wishlist->getId().'/delete');
        $this->client->submitForm('Agree');

        // Assert
        $this->assertResponseIsSuccessful();
        WishlistFactory::assert()->count(1);
        WishFactory::assert()->count(3);
    }
}
