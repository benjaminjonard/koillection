<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\RoleEnum;
use App\Enum\VisibilityEnum;
use App\Factory\CollectionFactory;
use App\Factory\ItemFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class CollectionCountersTest extends WebTestCase
{
    use Factories;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();

        $this->user = UserFactory::createOne(['username' => 'user', 'email' => 'user@test.com', 'roles' => [RoleEnum::ROLE_USER]])->object();
        $this->user2 = UserFactory::createOne(['username' => 'user2', 'email' => 'user2@test.com','roles' => [RoleEnum::ROLE_USER]])->object();
    }

    /*
     * When adding a new child, all parent counters must be increased by 1
     */
    public function test_add_child_collection(): void
    {
        $this->client->loginUser($this->user);

        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $this->user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $this->user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $this->user]);

        $crawler = $this->client->request('GET', '/collections/' . $collectionLevel1->getId());
        $this->assertEquals('2 collections', $crawler->filter('.nav-pills li')->eq(1)->text());

        $crawler = $this->client->request('GET', '/collections/' . $collectionLevel2->getId());
        $this->assertEquals('1 collection', $crawler->filter('.nav-pills li')->eq(1)->text());

        $crawler = $this->client->request('GET', '/collections/' . $collectionLevel3->getId());
        $this->assertEquals('0 collections', $crawler->filter('.nav-pills li')->eq(1)->text());
    }

    /*
     * When moving a child:
     * - Decrease all old parents collections counters by the number of children collection belonging to the child + 1 (itself)
     * - Decrease all old parents items counters by the number of items in the child and in all the child's children
     * - Increase all new parents collections counters by the number of children collection belonging to the child + 1 (itself)
     * - Increase all new parents items counters by the number of items in the child and in all the child's children
     */
    public function test_move_child_collection(): void
    {
        $this->client->loginUser($this->user);

        // Create a 4 level collection nesting, with 3 items in each collection
        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel1, 'owner' => $this->user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel2, 'owner' => $this->user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel3, 'owner' => $this->user]);
        $collectionLevel4 = CollectionFactory::createOne(['parent' => $collectionLevel3, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel4, 'owner' => $this->user]);

        // We move $collectionLevel3, which contains 1 collection (+ itself) and 6 items
        $newParentCollection = CollectionFactory::createOne(['parent' => null, 'owner' => $this->user]);
        $collectionLevel3->setParent($newParentCollection->object());

        $crawler = $this->client->request('GET', '/collections/' . $newParentCollection->getId());
        $this->assertEquals('6 items', $crawler->filter('.nav-pills li')->eq(0)->text());
        $this->assertEquals('2 collections', $crawler->filter('.nav-pills li')->eq(1)->text());

        $crawler = $this->client->request('GET', '/collections/' . $collectionLevel1->getId());
        $this->assertEquals('6 items', $crawler->filter('.nav-pills li')->eq(0)->text());
        $this->assertEquals('1 collection', $crawler->filter('.nav-pills li')->eq(1)->text());

        $crawler = $this->client->request('GET', '/collections/' . $collectionLevel2->getId());
        $this->assertEquals('3 items', $crawler->filter('.nav-pills li')->eq(0)->text());
        $this->assertEquals('0 collections', $crawler->filter('.nav-pills li')->eq(1)->text());

        $crawler = $this->client->request('GET', '/collections/' . $collectionLevel3->getId());
        $this->assertEquals('6 items', $crawler->filter('.nav-pills li')->eq(0)->text());
        $this->assertEquals('1 collection', $crawler->filter('.nav-pills li')->eq(1)->text());

        $crawler = $this->client->request('GET', '/collections/' . $collectionLevel4->getId());
        $this->assertEquals('3 items', $crawler->filter('.nav-pills li')->eq(0)->text());
        $this->assertEquals('0 collections', $crawler->filter('.nav-pills li')->eq(1)->text());
    }

    /*
   * When deleting a child:
   * - Decrease all old parents collections counters by the number of children collection belonging to the child + 1 (itself)
   * - Decrease all old parents items counters by the number of items in the child and in all the child's children
   */
    public function test_delete_child_collection(): void
    {
        $this->client->loginUser($this->user);
        
        // Create a 4 level collection nesting, with 3 items in each collection
        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel1, 'owner' => $this->user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel2, 'owner' => $this->user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel3, 'owner' => $this->user]);
        $collectionLevel4 = CollectionFactory::createOne(['parent' => $collectionLevel3, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel4, 'owner' => $this->user]);
        $collectionLevel3->remove();

        $crawler = $this->client->request('GET', '/collections/' . $collectionLevel1->getId());
        $this->assertEquals('6 items', $crawler->filter('.nav-pills li')->eq(0)->text());
        $this->assertEquals('1 collection', $crawler->filter('.nav-pills li')->eq(1)->text());

        $crawler = $this->client->request('GET', '/collections/' . $collectionLevel2->getId());
        $this->assertEquals('3 items', $crawler->filter('.nav-pills li')->eq(0)->text());
        $this->assertEquals('0 collections', $crawler->filter('.nav-pills li')->eq(1)->text());
    }
}
