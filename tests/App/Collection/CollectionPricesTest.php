<?php

declare(strict_types=1);

namespace App\Tests\App\Collection;

use App\Entity\Collection;
use App\Entity\Item;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use App\Service\CachedValuesGetter;
use App\Service\RefreshCachedValuesQueue;
use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CollectionPricesTest extends AppTestCase
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

    public function test_move_child_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne();

        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collectionLevel1, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item1, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item1, 'owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $item2 = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item2, 'owner' => $user]);

        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $item3 = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item3, 'owner' => $user]);

        $item3WithOtherCurrency = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'JPY', 'label' => 'Other currency', 'value' => '1000', 'item' => $item3WithOtherCurrency, 'owner' => $user]);

        $collectionLevel4 = CollectionFactory::createOne(['parent' => $collectionLevel3, 'owner' => $user]);
        $item4 = ItemFactory::createOne(['collection' => $collectionLevel4, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item4, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item4, 'owner' => $user]);

        // Act
        $newParentCollection = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel3->setParent($newParentCollection);
        \Zenstruck\Foundry\Persistence\save($collectionLevel3);

        \Zenstruck\Foundry\Persistence\refresh($collectionLevel1);
        \Zenstruck\Foundry\Persistence\refresh($collectionLevel2);
        \Zenstruck\Foundry\Persistence\refresh($collectionLevel3);
        \Zenstruck\Foundry\Persistence\refresh($collectionLevel4);
        \Zenstruck\Foundry\Persistence\refresh($newParentCollection);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEqualsWithDelta(200.0, $this->cachedValuesGetter->getCachedValues($newParentCollection)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(2000.0, $this->cachedValuesGetter->getCachedValues($newParentCollection)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1000.0, $this->cachedValuesGetter->getCachedValues($newParentCollection)['prices']['Other currency']['JPY'], PHP_FLOAT_EPSILON);

        $this->assertEqualsWithDelta(200.0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(2000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);

        $this->assertEqualsWithDelta(100.0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);

        $this->assertEqualsWithDelta(200.0, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(2000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['prices']['Other currency']['JPY'], PHP_FLOAT_EPSILON);

        $this->assertEqualsWithDelta(100.0, $this->cachedValuesGetter->getCachedValues($collectionLevel4)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel4)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);
    }

    public function test_delete_child_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne();

        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collectionLevel1, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item1, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item1, 'owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $item2 = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item2, 'owner' => $user]);

        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $item3 = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item3, 'owner' => $user]);

        $collectionLevel4 = CollectionFactory::createOne(['parent' => $collectionLevel3, 'owner' => $user]);
        $item4 = ItemFactory::createOne(['collection' => $collectionLevel4, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item4, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item4, 'owner' => $user]);

        // Act
        \Zenstruck\Foundry\Persistence\delete($collectionLevel3);
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEqualsWithDelta(200.0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(2000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);

        $this->assertEqualsWithDelta(100.0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);
    }

    public function test_add_item(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user, 'quantity' => 2]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item, 'owner' => $user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEqualsWithDelta(200.0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(2000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);

        $this->assertEqualsWithDelta(200.0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(2000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);

        $this->assertEqualsWithDelta(200.0, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(2000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel3)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);
    }

    public function test_move_item(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item, 'owner' => $user]);

        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $itemToMove = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $itemToMove, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $itemToMove, 'owner' => $user]);

        // Act
        $newCollection = CollectionFactory::createOne(['owner' => $user]);
        $itemToMove->setCollection($newCollection);
        \Zenstruck\Foundry\Persistence\save($itemToMove);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEqualsWithDelta(100.0, $this->cachedValuesGetter->getCachedValues($newCollection)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1000.0, $this->cachedValuesGetter->getCachedValues($newCollection)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);

        $this->assertEqualsWithDelta(100.0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);

        $this->assertEqualsWithDelta(100.0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);

        $this->assertArrayNotHasKey('Original price', $this->cachedValuesGetter->getCachedValues($collectionLevel3)['prices']);
        $this->assertArrayNotHasKey('Current price', $this->cachedValuesGetter->getCachedValues($collectionLevel3)['prices']);
    }

    public function test_delete_item(): void
    {
        // Arrange
        $user = UserFactory::createOne();

        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $item, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $item, 'owner' => $user]);

        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $itemToRemove = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Original price', 'value' => '100', 'item' => $itemToRemove, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Current price', 'value' => '1000', 'item' => $itemToRemove, 'owner' => $user]);

        // Act
        \Zenstruck\Foundry\Persistence\delete($itemToRemove);
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEqualsWithDelta(100.0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel1)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);

        $this->assertEqualsWithDelta(100.0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['prices']['Original price']['EUR'], PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1000.0, $this->cachedValuesGetter->getCachedValues($collectionLevel2)['prices']['Current price']['EUR'], PHP_FLOAT_EPSILON);

        $this->assertArrayNotHasKey('Original price', $this->cachedValuesGetter->getCachedValues($collectionLevel3)['prices']);
        $this->assertArrayNotHasKey('Current price', $this->cachedValuesGetter->getCachedValues($collectionLevel3)['prices']);
    }

    public function test_prices_exclude_private_item_for_anonymous(): void
    {
        $user = UserFactory::createOne();
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        $publicItem = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Price', 'value' => '100', 'item' => $publicItem, 'owner' => $user]);
        $privateItem = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Price', 'value' => '1000', 'item' => $privateItem, 'owner' => $user]);

        $this->refreshCachedValuesQueue->process();
        \Zenstruck\Foundry\Persistence\refresh($collection);

        $this->assertEqualsWithDelta(100.0, $this->cachedValuesGetter->getCachedValues($collection)['prices']['Price']['EUR'], PHP_FLOAT_EPSILON);
    }

    public function test_prices_exclude_item_in_private_child_collection_for_anonymous(): void
    {
        $user = UserFactory::createOne();
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        $privateChild = CollectionFactory::createOne(['parent' => $collection, 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        $item = ItemFactory::createOne(['collection' => $privateChild, 'owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'currency' => 'EUR', 'label' => 'Price', 'value' => '1000', 'item' => $item, 'owner' => $user]);

        $this->refreshCachedValuesQueue->process();
        \Zenstruck\Foundry\Persistence\refresh($collection);

        $this->assertArrayNotHasKey('Price', $this->cachedValuesGetter->getCachedValues($collection)['prices']);
    }
}
