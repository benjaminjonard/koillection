<?php

declare(strict_types=1);

namespace App\Tests\App\Datum;

use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\TestWith;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DatumVisibilityTest extends AppTestCase
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
    public function test_visibility_add_datum_to_collection(string $collection1Visibility, string $collection2Visibility, string $datumFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user, 'visibility' => $collection1Visibility]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection2Visibility]);

        // Act
        $datum = DatumFactory::createOne(['owner' => $user, 'collection' => $collectionLevel2]);

        // Assert
        DatumFactory::assert()->exists(['id' => $datum->getId(), 'finalVisibility' => $datumFinalVisibility]);
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
    public function test_visibility_add_datum_to_item(string $collectionVisibility, string $itemVisibility, string $datumFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => $collectionVisibility]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'visibility' => $itemVisibility]);

        // Act
        $datum = DatumFactory::createOne(['owner' => $user, 'item' => $item]);

        // Assert
        DatumFactory::assert()->exists(['id' => $datum->getId(), 'finalVisibility' => $datumFinalVisibility]);
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
    public function test_visibility_change_datum_collection(string $collection1Visibility, string $collection2Visibility, string $datumFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $oldCollection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne(['collection' => $oldCollection, 'owner' => $user]);

        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user, 'visibility' => $collection1Visibility]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection2Visibility]);

        // Act
        $datum->setCollection($collectionLevel2->object());
        $datum->save();

        // Assert
        DatumFactory::assert()->exists(['id' => $datum->getId(), 'finalVisibility' => $datumFinalVisibility]);
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
    public function test_visibility_change_datum_item(string $collectionVisibility, string $itemVisibility, string $datumFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => $collectionVisibility]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $datum = DatumFactory::createOne(['item' => $item, 'owner' => $user]);
        $newItem = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'visibility' => $itemVisibility]);

        // Act
        $datum->setItem($newItem->object());
        $datum->save();

        // Assert
        DatumFactory::assert()->exists(['id' => $datum->getId(), 'finalVisibility' => $datumFinalVisibility]);
    }
}
