<?php

declare(strict_types=1);

namespace App\Tests\App\Wishlist;

use App\Enum\VisibilityEnum;
use App\Factory\UserFactory;
use App\Tests\Factory\WishlistFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class WishlistTest extends WebTestCase
{
    use Factories;

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

        // Act
        $this->client->request('GET', '/wishlists/add');

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
        $wishlist = WishlistFactory::createOne(['owner' => $user]);

        // Act
        $this->client->request('GET', '/wishlists/'.$wishlist->getId().'/edit');
        $crawler = $this->client->submitForm('Submit', [
            'wishlist[name]' => 'Video games',
            'wishlist[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC
        ]);

        // Assert
        $this->assertSame('Video games', $crawler->filter('h1')->text());
    }
}
