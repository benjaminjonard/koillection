<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\VisibilityEnum;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\WishlistFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SearchTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_use_search(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $now = new \DateTimeImmutable();
        $collectionFrieren = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user, 'createdAt' => $now])->object();
        ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collectionFrieren, 'owner' => $user, 'createdAt' => $now]);
        TagFactory::createOne(['label' => 'Frieren', 'owner' => $user, 'createdAt' => $now]);
        WishlistFactory::createOne(['name' => 'Wishlist Frieren', 'owner' => $user, 'createdAt' => $now]);
        AlbumFactory::createOne(['title' => 'Frieren collection', 'owner' => $user, 'createdAt' => $now]);

        $collectionBerserk = CollectionFactory::createOne(['title' => 'Berserk', 'owner' => $user, 'createdAt' => $now])->object();
        ItemFactory::createOne(['name' => 'Berserk #1', 'collection' => $collectionBerserk, 'owner' => $user, 'createdAt' => $now]);
        TagFactory::createOne(['label' => 'Berserk', 'owner' => $user, 'createdAt' => $now]);
        WishlistFactory::createOne(['name' => 'Wishlist Berserk', 'owner' => $user, 'createdAt' => $now]);
        AlbumFactory::createOne(['title' => 'Berserk collection', 'owner' => $user, 'createdAt' => $now]);

        // Act
        $this->client->request('GET', '/search');
        $crawler = $this->client->submitForm('Submit', [
            'search[term]' => 'frie',
            'search[createdAt]' => $now->format('Y-m-d'),
            'search[searchInCollections]' => 1,
            'search[searchInItems]' => 1,
            'search[searchInTags]' => 1,
            'search[searchInWishlists]' => 1,
            'search[searchInAlbums]' => 1,
        ], 'GET');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Items', $crawler->filter('h2')->eq(0)->text());
        $this->assertCount(1, $crawler->filter('.collection-item'));

        $this->assertSame('Collections', $crawler->filter('h2')->eq(1)->text());
        $this->assertCount(1, $crawler->filter('.grid-container-collections')->eq(0)->filter('.collection-element'));

        $this->assertSame('Tags', $crawler->filter('h2')->eq(2)->text());
        $this->assertCount(1, $crawler->filter('.list-element'));

        $this->assertSame('Albums', $crawler->filter('h2')->eq(3)->text());
        $this->assertCount(1, $crawler->filter('.grid-container-collections')->eq(1)->filter('.collection-element'));

        $this->assertSame('Wishlists', $crawler->filter('h2')->eq(4)->text());
        $this->assertCount(1, $crawler->filter('.grid-container-collections')->eq(2)->filter('.collection-element'));
    }

    public function test_anonymous_user_search_public(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionFrieren = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user])->object();
        ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collectionFrieren, 'owner' => $user]);
        TagFactory::createOne(['label' => 'Frieren', 'owner' => $user]);
        WishlistFactory::createOne(['name' => 'Wishlist Frieren', 'owner' => $user]);
        AlbumFactory::createOne(['title' => 'Frieren collection', 'owner' => $user]);

        // Act
        $this->client->request('GET', '/user/'.$user->getUsername().'/search');
        $crawler = $this->client->submitForm('Submit', [
            'search[term]' => 'fri',
            'search[searchInCollections]' => 1,
            'search[searchInItems]' => 1,
            'search[searchInTags]' => 1,
            'search[searchInWishlists]' => 1,
            'search[searchInAlbums]' => 1,
        ], 'GET');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Items', $crawler->filter('h2')->eq(0)->text());
        $this->assertCount(1, $crawler->filter('.collection-item'));

        $this->assertSame('Collections', $crawler->filter('h2')->eq(1)->text());
        $this->assertCount(1, $crawler->filter('.grid-container-collections')->eq(0)->filter('.collection-element'));

        $this->assertSame('Tags', $crawler->filter('h2')->eq(2)->text());
        $this->assertCount(1, $crawler->filter('.list-element'));

        $this->assertSame('Albums', $crawler->filter('h2')->eq(3)->text());
        $this->assertCount(1, $crawler->filter('.grid-container-collections')->eq(1)->filter('.collection-element'));

        $this->assertSame('Wishlists', $crawler->filter('h2')->eq(4)->text());
        $this->assertCount(1, $crawler->filter('.grid-container-collections')->eq(2)->filter('.collection-element'));
    }

    public function test_anonymous_user_search_entities_private(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionFrieren = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE])->object();
        ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collectionFrieren, 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        TagFactory::createOne(['label' => 'Frieren', 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        WishlistFactory::createOne(['name' => 'Wishlist Frieren', 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        AlbumFactory::createOne(['title' => 'Frieren collection', 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->request('GET', '/user/'.$user->getUsername().'/search');
        $crawler = $this->client->submitForm('Submit', [
            'search[term]' => 'fri',
            'search[searchInCollections]' => 1,
            'search[searchInItems]' => 1,
            'search[searchInTags]' => 1,
            'search[searchInWishlists]' => 1,
            'search[searchInAlbums]' => 1,
        ], 'GET');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('No results', $crawler->filter('.main .content-block')->eq(1)->text());
    }

    public function test_anonymous_user_private(): void
    {
        // Arrange
        $user = UserFactory::createOne(['visibility' => VisibilityEnum::VISIBILITY_PRIVATE])->object();

        // Act
        $this->client->request('GET', '/user/'.$user->getUsername().'/search');

        // Assert
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function test_can_use_search_autocomplete(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user])->object();
        $item = ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user]);
        $tag = TagFactory::createOne(['label' => 'Frieren', 'owner' => $user]);
        $wishlist = WishlistFactory::createOne(['name' => 'Wishlist Frieren', 'owner' => $user]);
        $album = AlbumFactory::createOne(['title' => 'Frieren collection', 'owner' => $user]);

        // Act
        $this->client->request('GET', '/search/autocomplete/fri');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame(5, $content['totalResultsCounter']);
        $this->assertContains(['label' => 'Frieren', 'type' => 'collection', 'url' => '/collections/'.$collection->getId()], $content['results']);
        $this->assertContains(['label' => 'Frieren #1', 'type' => 'item', 'url' => '/items/'.$item->getId()], $content['results']);
        $this->assertContains(['label' => 'Frieren', 'type' => 'tag', 'url' => '/tags/'.$tag->getId()], $content['results']);
        $this->assertContains(['label' => 'Wishlist Frieren', 'type' => 'wishlist', 'url' => '/wishlists/'.$wishlist->getId()], $content['results']);
        $this->assertContains(['label' => 'Frieren collection', 'type' => 'album', 'url' => '/albums/'.$album->getId()], $content['results']);
    }

    public function test_anonymous_search_autocomplete_entities_public(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user])->object();
        $item = ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user]);
        $tag = TagFactory::createOne(['label' => 'Frieren', 'owner' => $user]);
        $wishlist = WishlistFactory::createOne(['name' => 'Wishlist Frieren', 'owner' => $user]);
        $album = AlbumFactory::createOne(['title' => 'Frieren collection', 'owner' => $user]);

        // Act
        $this->client->request('GET', '/user/'.$user->getUsername().'/search/autocomplete/fri');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame(5, $content['totalResultsCounter']);
        $this->assertContains(['label' => 'Frieren', 'type' => 'collection', 'url' => '/collections/'.$collection->getId()], $content['results']);
        $this->assertContains(['label' => 'Frieren #1', 'type' => 'item', 'url' => '/items/'.$item->getId()], $content['results']);
        $this->assertContains(['label' => 'Frieren', 'type' => 'tag', 'url' => '/tags/'.$tag->getId()], $content['results']);
        $this->assertContains(['label' => 'Wishlist Frieren', 'type' => 'wishlist', 'url' => '/wishlists/'.$wishlist->getId()], $content['results']);
        $this->assertContains(['label' => 'Frieren collection', 'type' => 'album', 'url' => '/albums/'.$album->getId()], $content['results']);
    }

    public function test_anonymous_search_autocomplete_entities_private(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE])->object();
        $item = ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        $tag = TagFactory::createOne(['label' => 'Frieren', 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        $wishlist = WishlistFactory::createOne(['name' => 'Wishlist Frieren', 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        $album = AlbumFactory::createOne(['title' => 'Frieren collection', 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->request('GET', '/user/'.$user->getUsername().'/search/autocomplete/fri');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame(0, $content['totalResultsCounter']);
    }

    public function test_anonymous_search_autocomplete_user_private(): void
    {
        // Arrange
        $user = UserFactory::createOne(['visibility' => VisibilityEnum::VISIBILITY_PRIVATE])->object();

        // Act
        $this->client->request('GET', '/user/'.$user->getUsername().'/search/autocomplete');

        // Assert
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }
}
