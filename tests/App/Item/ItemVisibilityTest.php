<?php

declare(strict_types=1);

namespace App\Tests\App\Item;

use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\TestWith;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ItemVisibilityTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    #[TestWith(['public', 'public', 'public'])]
    #[TestWith(['public', 'private', 'private'])]
    #[TestWith(['public', 'internal', 'internal'])]

    #[TestWith(['internal', 'public', 'internal'])]
    #[TestWith(['internal', 'private', 'private'])]
    #[TestWith(['internal', 'internal', 'internal'])]

    #[TestWith(['private', 'public', 'private'])]
    #[TestWith(['private', 'private', 'private'])]
    #[TestWith(['private', 'internal', 'private'])]
    public function test_visibility_add_item(string $collection1Visibility, string $collection2Visibility, string $itemFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user, 'visibility' => $collection1Visibility]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection2Visibility]);

        // Act
        $item = ItemFactory::createOne(['owner' => $user, 'collection' => $collectionLevel2]);

        // Assert
        ItemFactory::assert()->exists(['id' => $item->getId(), 'finalVisibility' => $itemFinalVisibility]);
    }


    #[TestWith(['public', 'public', 'public'])]
    #[TestWith(['public', 'private', 'private'])]
    #[TestWith(['public', 'internal', 'internal'])]

    #[TestWith(['internal', 'public', 'internal'])]
    #[TestWith(['internal', 'private', 'private'])]
    #[TestWith(['internal', 'internal', 'internal'])]

    #[TestWith(['private', 'public', 'private'])]
    #[TestWith(['private', 'private', 'private'])]
    #[TestWith(['private', 'internal', 'private'])]
    public function test_visibility_change_item_collection(string $collection1Visibility, string $collection2Visibility, string $itemFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $oldCollection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $oldCollection, 'owner' => $user]);

        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user, 'visibility' => $collection1Visibility]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection2Visibility]);

        // Act
        $item->setCollection($collectionLevel2->object());
        $item->save();

        // Assert
        ItemFactory::assert()->exists(['id' => $item->getId(), 'finalVisibility' => $itemFinalVisibility]);
    }
}
