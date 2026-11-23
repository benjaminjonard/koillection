<?php

declare(strict_types=1);

namespace App\Tests\App\Album;

use App\Entity\Album;
use App\Enum\VisibilityEnum;
use App\Service\CachedValuesGetter;
use App\Service\RefreshCachedValuesQueue;
use App\Tests\AppTestCase;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\PhotoFactory;
use App\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AlbumCountersTest extends AppTestCase
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
    public function test_add_child_album(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(2, $this->cachedValuesGetter->getCachedValues($albumLevel1)['counters']['children']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($albumLevel2)['counters']['children']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel3)['counters']['children']);
    }

    /*
     * When moving a child:
     * - Decrease all old parents albums counters by the number of children album belonging to the child + 1 (itself)
     * - Decrease all old parents photos counters by the number of photos in the child and in all the child's children
     * - Increase all new parents albums counters by the number of children album belonging to the child + 1 (itself)
     * - Increase all new parents photos counters by the number of photos in the child and in all the child's children
     */
    public function test_move_child_album(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $albumLevel1 = AlbumFactory::createOne(['parent' => null, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel1, 'owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel2, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel3, 'owner' => $user]);
        $albumLevel4 = AlbumFactory::createOne(['parent' => $albumLevel3, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel4, 'owner' => $user]);

        // Act
        $newParentAlbum = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel3->setParent($newParentAlbum);
        \Zenstruck\Foundry\Persistence\save($albumLevel3);

        \Zenstruck\Foundry\Persistence\refresh($albumLevel1);
        \Zenstruck\Foundry\Persistence\refresh($albumLevel2);
        \Zenstruck\Foundry\Persistence\refresh($albumLevel3);
        \Zenstruck\Foundry\Persistence\refresh($albumLevel4);
        \Zenstruck\Foundry\Persistence\refresh($newParentAlbum);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($newParentAlbum)['counters']['photos']);
        $this->assertSame(2, $this->cachedValuesGetter->getCachedValues($newParentAlbum)['counters']['children']);

        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($albumLevel1)['counters']['photos']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($albumLevel1)['counters']['children']);

        $this->assertSame(3, $this->cachedValuesGetter->getCachedValues($albumLevel2)['counters']['photos']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel2)['counters']['children']);

        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($albumLevel3)['counters']['photos']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($albumLevel3)['counters']['children']);

        $this->assertSame(3, $this->cachedValuesGetter->getCachedValues($albumLevel4)['counters']['photos']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel4)['counters']['children']);
    }

    /*
   * When deleting a child:
   * - Decrease all old parents albums counters by the number of children album belonging to the child + 1 (itself)
   * - Decrease all old parents photos counters by the number of photos in the child and in all the child's children
   */
    public function test_delete_child_album(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel1, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel1, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel1, 'owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel2, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel2, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel2, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel3, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel3, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel3, 'owner' => $user]);
        $albumLevel4 = AlbumFactory::createOne(['parent' => $albumLevel3, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel4, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel4, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel4, 'owner' => $user]);

        // Act
        \Zenstruck\Foundry\Persistence\delete($albumLevel3);
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($albumLevel1)['counters']['photos']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($albumLevel1)['counters']['children']);

        $this->assertSame(3, $this->cachedValuesGetter->getCachedValues($albumLevel2)['counters']['photos']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel2)['counters']['children']);
    }

    /*
     * When adding a new photo, all parent counters must be increased by 1
     */
    public function test_add_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel3, 'owner' => $user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($albumLevel1)['counters']['photos']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($albumLevel2)['counters']['photos']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($albumLevel3)['counters']['photos']);
    }

    /*
     * When moving a photo, all parent new counters must be increased by 1 and old parent counters decreased by 1
     */
    public function test_move_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);
        $photo = PhotoFactory::createOne(['album' => $albumLevel3, 'owner' => $user]);

        // Act
        $newAlbum = AlbumFactory::createOne(['owner' => $user]);
        $photo->setAlbum($newAlbum);
        \Zenstruck\Foundry\Persistence\save($photo);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($newAlbum)['counters']['photos']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel1)['counters']['photos']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel2)['counters']['photos']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel3)['counters']['photos']);
    }

    /*
     * When deleting an photo decrease all old parents albums counters by one
     */
    public function test_delete_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);
        $photo = PhotoFactory::createOne(['album' => $albumLevel3, 'owner' => $user]);

        // Act
        \Zenstruck\Foundry\Persistence\delete($photo);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel1)['counters']['photos']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel2)['counters']['photos']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel3)['counters']['photos']);
    }

    public function test_change_photo_visibility(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        $photo = PhotoFactory::createOne(['album' => $albumLevel2, 'owner' => $user]);
        $this->refreshCachedValuesQueue->process();

        // Act
        $photo->setVisibility(VisibilityEnum::VISIBILITY_PRIVATE);
        \Zenstruck\Foundry\Persistence\save($photo);

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel1)['counters']['photos']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($albumLevel2)['counters']['photos']);
    }
}
