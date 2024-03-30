<?php

declare(strict_types=1);

namespace App\Tests\App\Collection;

use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\TestWith;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CollectionVisibilityTest extends AppTestCase
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
    public function test_visibility_add_nested_collection(string $collection1Visibility, string $collection3Visibility, string $collection2FinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user, 'visibility' => $collection1Visibility]);

        // Act
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection3Visibility]);

        // Assert
        CollectionFactory::assert()->exists(['id' => $collectionLevel2->getId(), 'finalVisibility' => $collection2FinalVisibility]);
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
    public function test_visibility_change_parent_collection(string $newCollectionVisibility, string $collection2Visibility, string $collection1FinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection2Visibility]);
        $item2 = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);

        // Act
        $newParentCollection = CollectionFactory::createOne(['owner' => $user, 'visibility' => $newCollectionVisibility]);
        $collectionLevel2->setParent($newParentCollection->object());
        $collectionLevel2->save();

        // Assert
        CollectionFactory::assert()->exists(['id' => $collectionLevel2->getId(), 'finalVisibility' => $collection1FinalVisibility]);
        ItemFactory::assert()->exists(['id' => $item2->getId(), 'finalVisibility' => $collection1FinalVisibility]);
    }

    #[TestWith(['public', 'public', 'public', 'public'])]
    #[TestWith(['public', 'private', 'public', 'private'])]
    #[TestWith(['public', 'internal', 'public', 'internal'])]

    #[TestWith(['internal', 'public', 'internal', 'internal'])]
    #[TestWith(['internal', 'private', 'internal', 'private'])]
    #[TestWith(['internal', 'internal', 'internal', 'internal'])]

    #[TestWith(['private', 'public', 'private', 'private'])]
    #[TestWith(['private', 'private', 'private', 'private'])]
    #[TestWith(['private', 'internal', 'private', 'private'])]
    public function test_visibility_change_collection_visibility(string $collection1Visibility, string $collection2Visibility, string $level1Visibility, string $level2Visibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collectionLevel1, 'owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection2Visibility]);
        $item2 = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);

        // Act
        $collectionLevel1->setVisibility($collection1Visibility);
        $collectionLevel1->save();

        // Assert
        CollectionFactory::assert()->exists(['id' => $collectionLevel1->getId(), 'finalVisibility' => $level1Visibility]);
        ItemFactory::assert()->exists(['id' => $item1->getId(), 'finalVisibility' => $level1Visibility]);
        CollectionFactory::assert()->exists(['id' => $collectionLevel2->getId(), 'finalVisibility' => $level2Visibility]);
        ItemFactory::assert()->exists(['id' => $item2->getId(), 'finalVisibility' => $level2Visibility]);
    }
}
