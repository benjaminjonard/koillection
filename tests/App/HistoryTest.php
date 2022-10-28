<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\ChoiceListFactory;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\InventoryFactory;
use App\Tests\Factory\LogFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\PhotoFactory;
use App\Tests\Factory\TagCategoryFactory;
use App\Tests\Factory\TemplateFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\WishFactory;
use App\Tests\Factory\WishlistFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class HistoryTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_see_history(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collection, 'name' => 'Frieren #1', 'owner' => $user]);

        $tagCategory = TagCategoryFactory::createOne(['label' => 'Trip', 'owner' => $user]);
        TagFactory::createOne(['category' => $tagCategory, 'label' => 'Trip to Japan', 'owner' => $user]);

        $album = AlbumFactory::createOne(['title' => 'Home', 'owner' => $user]);
        PhotoFactory::createOne(['album' => $album, 'title' => 'Main shelf', 'owner' => $user]);

        $wishlist = WishlistFactory::createOne(['name' => 'Books', 'owner' => $user]);
        WishFactory::createOne(['wishlist' => $wishlist, 'name' => 'Frieren #2', 'owner' => $user]);

        TemplateFactory::createOne(['name' => 'Books', 'owner' => $user]);
        ChoiceListFactory::createOne(['name' => 'Status', 'owner' => $user]);
        InventoryFactory::createOne(['name' => 'Collection', 'owner' => $user]);

        $collection->remove();
        // Refresh logs because they are updated in LoggableListener with a native query, Foundry isn't aware of those changes
        foreach (LogFactory::all() as $log) {
            $log->refresh();
        }

        // Act
        $crawler = $this->client->request('GET', '/history?search_history[classes][]=collection&search_history[classes][]=item&search_history[classes][]=tag&search_history[classes][]=tagCategory&search_history[classes][]=album&search_history[classes][]=photo&search_history[classes][]=wishlist&search_history[classes][]=wish&search_history[classes][]=template&search_history[classes][]=choiceList&search_history[classes][]=inventory&search_history[types][]=create&search_history[types][]=delete');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('History', $crawler->filter('h1')->text());
        $this->assertCount(13, $crawler->filter('tbody tr'));

        $this->assertSame('Collection Frieren was created', $crawler->filter('tbody tr')->eq(12)->filter('td')->eq(1)->text());
        $this->assertSame('Item Frieren #1 was created', $crawler->filter('tbody tr')->eq(11)->filter('td')->eq(1)->text());

        $this->assertSame('Tag category Trip was created', $crawler->filter('tbody tr')->eq(10)->filter('td')->eq(1)->text());
        $this->assertSame('Tag Trip to Japan was created', $crawler->filter('tbody tr')->eq(9)->filter('td')->eq(1)->text());

        $this->assertSame('Album Home was created', $crawler->filter('tbody tr')->eq(8)->filter('td')->eq(1)->text());
        $this->assertSame('Photo Main shelf was created', $crawler->filter('tbody tr')->eq(7)->filter('td')->eq(1)->text());

        $this->assertSame('Wishlist Books was created', $crawler->filter('tbody tr')->eq(6)->filter('td')->eq(1)->text());
        $this->assertSame('Wish Frieren #2 was created', $crawler->filter('tbody tr')->eq(5)->filter('td')->eq(1)->text());

        $this->assertSame('Template Books was created', $crawler->filter('tbody tr')->eq(4)->filter('td')->eq(1)->text());
        $this->assertSame('Choice list Status was created', $crawler->filter('tbody tr')->eq(3)->filter('td')->eq(1)->text());
        $this->assertSame('Inventory Collection was created', $crawler->filter('tbody tr')->eq(2)->filter('td')->eq(1)->text());

        $this->assertSame('Item Frieren #1 was deleted', $crawler->filter('tbody tr')->eq(1)->filter('td')->eq(1)->text());
        $this->assertSame('Collection Frieren was deleted', $crawler->filter('tbody tr')->eq(0)->filter('td')->eq(1)->text());

    }

    public function test_ajax_history(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->xmlHttpRequest('GET', '/history');

        // Assert
        $this->assertResponseIsSuccessful();
    }
}
