<?php

declare(strict_types=1);

namespace App\Tests\App\Collection;

use App\Enum\DatumTypeEnum;
use App\Factory\CollectionFactory;
use App\Factory\ItemFactory;
use App\Factory\UserFactory;
use App\Service\RefreshCachedValuesQueue;
use App\Tests\Factory\DatumFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CollectionPricesTest extends WebTestCase
{
    use Factories, ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->refreshCachedValuesQueue = $this->getContainer()->get(RefreshCachedValuesQueue::class);
    }

    public function test_move_child_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collectionLevel1, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item1, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item1, 'owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $item2 = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item2, 'owner' => $user]);

        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $item3 = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item3, 'owner' => $user]);

        $collectionLevel4 = CollectionFactory::createOne(['parent' => $collectionLevel3, 'owner' => $user]);
        $item4 = ItemFactory::createOne(['collection' => $collectionLevel4, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item4, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item4, 'owner' => $user]);

        // Act
        $newParentCollection = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel3->setParent($newParentCollection->object());
        $collectionLevel3->save();

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(200.0, $newParentCollection->getCachedValues()['prices']['Original price']);
        $this->assertSame(2000.0, $newParentCollection->getCachedValues()['prices']['Current price']);

        $this->assertSame(200.0, $collectionLevel1->getCachedValues()['prices']['Original price']);
        $this->assertSame(2000.0, $collectionLevel1->getCachedValues()['prices']['Current price']);

        $this->assertSame(100.0, $collectionLevel2->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $collectionLevel2->getCachedValues()['prices']['Current price']);

        $this->assertSame(200.0, $collectionLevel3->getCachedValues()['prices']['Original price']);
        $this->assertSame(2000.0, $collectionLevel3->getCachedValues()['prices']['Current price']);

        $this->assertSame(100.0, $collectionLevel4->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $collectionLevel4->getCachedValues()['prices']['Current price']);
    }

    public function test_delete_child_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collectionLevel1, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item1, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item1, 'owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $item2 = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item2, 'owner' => $user]);

        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $item3 = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item3, 'owner' => $user]);

        $collectionLevel4 = CollectionFactory::createOne(['parent' => $collectionLevel3, 'owner' => $user]);
        $item4 = ItemFactory::createOne(['collection' => $collectionLevel4, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item4, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item4, 'owner' => $user]);

        // Act
        $collectionLevel3->remove();
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(200.0, $collectionLevel1->getCachedValues()['prices']['Original price']);
        $this->assertSame(2000.0, $collectionLevel1->getCachedValues()['prices']['Current price']);

        $this->assertSame(100.0, $collectionLevel2->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $collectionLevel2->getCachedValues()['prices']['Current price']);
    }

    public function test_add_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item, 'owner' => $user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(100.0, $collectionLevel1->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $collectionLevel1->getCachedValues()['prices']['Current price']);

        $this->assertSame(100.0, $collectionLevel2->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $collectionLevel2->getCachedValues()['prices']['Current price']);

        $this->assertSame(100.0, $collectionLevel3->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $collectionLevel3->getCachedValues()['prices']['Current price']);
    }

    public function test_move_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item, 'owner' => $user]);

        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $itemToMove = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $itemToMove, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $itemToMove, 'owner' => $user]);;

        // Act
        $newCollection = CollectionFactory::createOne(['owner' => $user]);
        $itemToMove->setCollection($newCollection->object());
        $itemToMove->save();

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(100.0, $newCollection->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $newCollection->getCachedValues()['prices']['Current price']);

        $this->assertSame(100.0, $collectionLevel1->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $collectionLevel1->getCachedValues()['prices']['Current price']);

        $this->assertSame(100.0, $collectionLevel2->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $collectionLevel2->getCachedValues()['prices']['Current price']);

        $this->assertArrayNotHasKey('Original price', $collectionLevel3->getCachedValues()['prices']);
        $this->assertArrayNotHasKey('Current price', $collectionLevel3->getCachedValues()['prices']);
    }

    public function test_delete_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $item, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $item, 'owner' => $user]);

        $collectionLevel3 = CollectionFactory::createOne(['parent' => $collectionLevel2, 'owner' => $user]);
        $itemToRemove = ItemFactory::createOne(['collection' => $collectionLevel3, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Original price', 'value' => '100', 'item' => $itemToRemove, 'owner' => $user]);
        DatumFactory::createOne(['type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Current price', 'value' => '1000', 'item' => $itemToRemove, 'owner' => $user]);

        // Act
        $itemToRemove->remove();
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(100.0, $collectionLevel1->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $collectionLevel1->getCachedValues()['prices']['Current price']);

        $this->assertSame(100.0, $collectionLevel2->getCachedValues()['prices']['Original price']);
        $this->assertSame(1000.0, $collectionLevel2->getCachedValues()['prices']['Current price']);

        $this->assertArrayNotHasKey('Original price', $collectionLevel3->getCachedValues()['prices']);
        $this->assertArrayNotHasKey('Current price', $collectionLevel3->getCachedValues()['prices']);
    }
}
