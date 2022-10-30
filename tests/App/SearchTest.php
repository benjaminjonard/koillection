<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Entity\Wishlist;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\TagFactory;
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
        $collectionFrieren = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user])->object();
        ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collectionFrieren, 'owner' => $user]);
        TagFactory::createOne(['label' => 'Frieren', 'owner' => $user]);
        WishlistFactory::createOne(['name' => 'Wishlist Frieren', 'owner' => $user]);
        AlbumFactory::createOne(['title' => 'Frieren collection', 'owner' => $user]);

        $collectionBerserk = CollectionFactory::createOne(['title' => 'Berserk', 'owner' => $user])->object();
        ItemFactory::createOne(['name' => 'Berserk #1', 'collection' => $collectionBerserk, 'owner' => $user]);
        TagFactory::createOne(['label' => 'Berserk', 'owner' => $user]);
        WishlistFactory::createOne(['name' => 'Wishlist Berserk', 'owner' => $user]);
        AlbumFactory::createOne(['title' => 'Berserk collection', 'owner' => $user]);

        // Act
        $this->client->request('GET', '/search');
        $crawler = $this->client->submitForm('Submit', [
            'search[term]' => 'frie',
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
        $this->assertContains(["label" => "Frieren", "type" => "collection", "url" => "/collections/".$collection->getId()], $content['results']);
        $this->assertContains(["label" => "Frieren #1", "type" => "item", "url" => "/items/".$item->getId()], $content['results']);
        $this->assertContains(["label" => "Frieren", "type" => "tag", "url" => "/tags/".$tag->getId()], $content['results']);
        $this->assertContains(["label" => "Wishlist Frieren", "type" => "wishlist", "url" => "/wishlists/".$wishlist->getId()], $content['results']);
        $this->assertContains(["label" => "Frieren collection", "type" => "album", "url" => "/albums/".$album->getId()], $content['results']);
    }
}
