<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\VisibilityEnum;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\WishFactory;
use App\Tests\Factory\WishlistFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WishTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_create_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $wishlist = WishlistFactory::createOne(['owner' => $user])->object();

        // Act
        $this->client->request('GET', '/wishes/add?wishlist='.$wishlist->getId());
        $this->client->submitForm('Submit', [
            'wish[name]' => 'Frieren #1',
            'wish[visibility]' => VisibilityEnum::VISIBILITY_PRIVATE,
            'wish[wishlist]' => $wishlist->getId(),
            'wish[url]' => 'https://fr.wikipedia.org/wiki/Frieren',
            'wish[price]' => '7.95',
            'wish[currency]' => 'EUR',
            'wish[comment]' => 'This is a comment'
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        WishFactory::assert()->exists([
            'name' => 'Frieren #1',
            'wishlist' => $wishlist->getId(),
            'visibility' => VisibilityEnum::VISIBILITY_PRIVATE,
            'owner' => $user,
            'url' => 'https://fr.wikipedia.org/wiki/Frieren',
            'price' => '7.95',
            'currency' => 'EUR',
            'comment' => 'This is a comment',
        ]);
    }

    public function test_cant_create_wish_without_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/wishes/add');

        // Assert
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function test_can_edit_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $wishlist = WishlistFactory::createOne(['owner' => $user])->object();
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $this->client->request('GET', '/wishes/'.$wish->getId().'/edit');
        $this->client->submitForm('Submit', [
            'wish[name]' => 'New name',
            'wish[visibility]' => VisibilityEnum::VISIBILITY_PRIVATE,
            'wish[wishlist]' => $wishlist->getId(),
            'wish[url]' => 'https://fr.wikipedia.org/wiki/Frieren',
            'wish[price]' => '7.95',
            'wish[currency]' => 'EUR',
            'wish[comment]' => 'This is a comment'
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        WishFactory::assert()->exists([
            'name' => 'New name',
            'wishlist' => $wishlist->getId(),
            'visibility' => VisibilityEnum::VISIBILITY_PRIVATE,
            'owner' => $user,
            'url' => 'https://fr.wikipedia.org/wiki/Frieren',
            'price' => '7.95',
            'currency' => 'EUR',
            'comment' => 'This is a comment',
        ]);
    }

    public function test_can_delete_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $wishlist = WishlistFactory::createOne(['owner' => $user])->object();
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/wishlists/'.$wishlist->getId());
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/wishes/'.$wish->getId().'/delete');
        $this->client->submitForm('Agree');

        // Assert
        $this->assertResponseIsSuccessful();
        WishlistFactory::assert()->count(1);
        WishFactory::assert()->notExists(0);
    }

    public function test_can_transform_wish_to_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $wishlist = WishlistFactory::createOne(['owner' => $user])->object();
        $wish = WishFactory::createOne(['name' => 'Frieren #1', 'wishlist' => $wishlist, 'owner' => $user]);
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user])->object();

        // Act
        $this->client->request('GET', '/wishes/'.$wish->getId().'/transfer');
        $crawler = $this->client->submitForm('Submit', [
            'item[name]' => 'Frieren #1',
            'item[visibility]' => VisibilityEnum::VISIBILITY_PRIVATE,
            'item[collection]' => $collection->getId(),
            'item[quantity]' => 1
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Frieren #1', $crawler->filter('h1')->innerText());
        ItemFactory::assert()->exists([
            'name' => 'Frieren #1',
            'collection' => $collection->getId(),
        ]);
        WishFactory::assert()->count(0);
    }
}
