<?php

declare(strict_types=1);

namespace App\Tests\App\Collection;

use App\Entity\Collection;
use App\Entity\Item;
use App\Enum\VisibilityEnum;
use App\Service\CachedValuesGetter;
use App\Service\RefreshCachedValuesQueue;
use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use function Zenstruck\Foundry\Persistence\save;

class CollectionCountersTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    public ?RefreshCachedValuesQueue $refreshCachedValuesQueue;

    public ?CachedValuesGetter $cachedValuesGetter;

    #[\Override]
    protected function setUp(): void
    {
        $this->refreshCachedValuesQueue = $this->getContainer()->get(RefreshCachedValuesQueue::class);
        $this->cachedValuesGetter = $this->getContainer()->get(CachedValuesGetter::class);
    }

    /*
     * When adding a new child, all parent counters must be increased by 1
     */
    public function test_add_child_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(2, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['children']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['counters']['children']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['counters']['children']);
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
        $user = UserFactory::createOne();
        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel1, 'owner' => $user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel2, 'owner' => $user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel3, 'owner' => $user]);
        $collectionLevel4 = CollectionFactory::createOne(['parent' => $collectionLevel3, 'owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $collectionLevel4, 'owner' => $user]);

        // Act
        $newParentCollection = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel3->setParent($newParentCollection);
        save($collectionLevel3);

        \Zenstruck\Foundry\Persistence\refresh($collectionLevel1);
        \Zenstruck\Foundry\Persistence\refresh($collectionLevel2);
        \Zenstruck\Foundry\Persistence\refresh($collectionLevel3);
        \Zenstruck\Foundry\Persistence\refresh($collectionLevel4);
        \Zenstruck\Foundry\Persistence\refresh($newParentCollection);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($newParentCollection)['counters']['items']);
        $this->assertSame(2, $this->cachedValuesGetter->getCachedValues($newParentCollection)['counters']['children']);

        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['items']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['children']);

        $this->assertSame(3, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['counters']['items']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['counters']['children']);

        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['counters']['items']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['counters']['children']);

        $this->assertSame(3, $this->cachedValuesGetter->getCachedValues($collectionLevel4)['counters']['items']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel4)['counters']['children']);
    }

    /*
     * When deleting a child:
     * - Decrease all old parents collections counters by the number of children collection belonging to the child + 1 (itself)
     * - Decrease all old parents items counters by the number of items in the child and in all the child's children
     */
    public function test_delete_child_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel1, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel1, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel1, 'owner' => $user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        $collectionLevel4 = CollectionFactory::createOne(['parent' => $collectionLevel3, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel4, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel4, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel4, 'owner' => $user]);

        // Act
        \Zenstruck\Foundry\Persistence\delete($collectionLevel3);
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['items']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['children']);

        $this->assertSame(3, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['counters']['items']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['counters']['children']);
    }

    /*
     * When adding a new item, all parent counters must be increased by 1
     */
    public function test_add_item(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['items']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['counters']['items']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['counters']['items']);
    }

    /*
     * When moving an item, all parent new counters must be increased by 1 and old parent counters decreased by 1
     */
    public function test_move_item(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);

        // Act
        $newCollection = CollectionFactory::createOne(['owner' => $user]);
        $item->setCollection($newCollection);
        save($item);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($newCollection)['counters']['items']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['items']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['counters']['items']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['counters']['items']);
    }

    /*
     * When deleting an item decrease all old parents collections counters by one
     */
    public function test_delete_item(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);

        // Act
        \Zenstruck\Foundry\Persistence\delete($item);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['items']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['counters']['items']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['counters']['items']);
    }

    public function test_change_item_visibility(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user, 'name' => 'Frieren #1']);
        $this->refreshCachedValuesQueue->process();

        // Act
        $item->setVisibility(VisibilityEnum::VISIBILITY_PRIVATE);
        save($item);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['items']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['counters']['items']);
    }

    public function test_change_child_collection_visibility(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user, 'name' => 'Frieren #1']);
        $this->refreshCachedValuesQueue->process();

        // Act
        $collectionLevel2->setVisibility(VisibilityEnum::VISIBILITY_PRIVATE);
        save($collectionLevel2);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['children']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['counters']['items']);
    }
}
