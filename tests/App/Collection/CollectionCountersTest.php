<?php

declare(strict_types=1);

namespace App\Tests\App\Collection;

use App\Enum\RoleEnum;
use App\Factory\CollectionFactory;
use App\Factory\ItemFactory;
use App\Factory\UserFactory;
use App\Service\RefreshCachedValuesQueue;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class CollectionCountersTest extends WebTestCase
{
    use Factories;

    protected function setUp(): void
    {
        $this->refreshCachedValuesQueue = $this->getContainer()->get(RefreshCachedValuesQueue::class);

        $this->user = UserFactory::createOne(['username' => 'user', 'email' => 'user@test.com', 'roles' => [RoleEnum::ROLE_USER]])->object();
    }

    /*
     * When adding a new child, all parent counters must be increased by 1
     */
    public function test_add_child_collection(): void
    {
        // Arrange
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $this->user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $this->user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $this->user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEquals(2, $collectionLevel1->getCachedValues()['counters']['children']);
        $this->assertEquals(1, $collectionLevel2->getCachedValues()['counters']['children']);
        $this->assertEquals(0, $collectionLevel3->getCachedValues()['counters']['children']);
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
        // Arrange
        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel1, 'owner' => $this->user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel2, 'owner' => $this->user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel3, 'owner' => $this->user]);
        $collectionLevel4 = CollectionFactory::createOne(['parent' => $collectionLevel3, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel4, 'owner' => $this->user]);

        // Act
        $newParentCollection = CollectionFactory::createOne(['owner' => $this->user]);
        $collectionLevel3->setParent($newParentCollection->object());
        $collectionLevel3->save();
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEquals(6, $newParentCollection->getCachedValues()['counters']['items']);
        $this->assertEquals(2, $newParentCollection->getCachedValues()['counters']['children']);

        $this->assertEquals(6, $collectionLevel1->getCachedValues()['counters']['items']);
        $this->assertEquals(1, $collectionLevel1->getCachedValues()['counters']['children']);

        $this->assertEquals(3, $collectionLevel2->getCachedValues()['counters']['items']);
        $this->assertEquals(0, $collectionLevel2->getCachedValues()['counters']['children']);

        $this->assertEquals(6, $collectionLevel3->getCachedValues()['counters']['items']);
        $this->assertEquals(1, $collectionLevel3->getCachedValues()['counters']['children']);

        $this->assertEquals(3, $collectionLevel4->getCachedValues()['counters']['items']);
        $this->assertEquals(0, $collectionLevel4->getCachedValues()['counters']['children']);
    }

    /*
   * When deleting a child:
   * - Decrease all old parents collections counters by the number of children collection belonging to the child + 1 (itself)
   * - Decrease all old parents items counters by the number of items in the child and in all the child's children
   */
    public function test_delete_child_collection(): void
    {
        // Arrange
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel1, 'owner' => $this->user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel2, 'owner' => $this->user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel3, 'owner' => $this->user]);
        $collectionLevel4 = CollectionFactory::createOne(['parent' => $collectionLevel3, 'owner' => $this->user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel4, 'owner' => $this->user]);

        // Act
        $collectionLevel3->remove();
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEquals(6, $collectionLevel1->getCachedValues()['counters']['items']);
        $this->assertEquals(1, $collectionLevel1->getCachedValues()['counters']['children']);

        $this->assertEquals(3, $collectionLevel2->getCachedValues()['counters']['items']);
        $this->assertEquals(0, $collectionLevel2->getCachedValues()['counters']['children']);
    }
}
