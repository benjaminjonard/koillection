<?php

declare(strict_types=1);

namespace App\Tests\App\Album;

use App\Service\RefreshCachedValuesQueue;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\PhotoFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AlbumCountersTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->refreshCachedValuesQueue = $this->getContainer()->get(RefreshCachedValuesQueue::class);
    }

    /*
     * When adding a new child, all parent counters must be increased by 1
     */
    public function test_add_child_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(2, $albumLevel1->getCachedValues()['counters']['children']);
        $this->assertSame(1, $albumLevel2->getCachedValues()['counters']['children']);
        $this->assertSame(0, $albumLevel3->getCachedValues()['counters']['children']);
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
        $user = UserFactory::createOne()->object();
        $albumLevel1 = AlbumFactory::createOne(['parent' => null, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel1, 'owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel2, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel3, 'owner' => $user]);
        $albumLevel4 = AlbumFactory::createOne(['parent' => $albumLevel3, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel4, 'owner' => $user]);

        // Act
        $user = UserFactory::createOne()->object();
        $newParentAlbum = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel3->setParent($newParentAlbum->object());
        $albumLevel3->save();

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(6, $newParentAlbum->getCachedValues()['counters']['photos']);
        $this->assertSame(2, $newParentAlbum->getCachedValues()['counters']['children']);

        $this->assertSame(6, $albumLevel1->getCachedValues()['counters']['photos']);
        $this->assertSame(1, $albumLevel1->getCachedValues()['counters']['children']);

        $this->assertSame(3, $albumLevel2->getCachedValues()['counters']['photos']);
        $this->assertSame(0, $albumLevel2->getCachedValues()['counters']['children']);

        $this->assertSame(6, $albumLevel3->getCachedValues()['counters']['photos']);
        $this->assertSame(1, $albumLevel3->getCachedValues()['counters']['children']);

        $this->assertSame(3, $albumLevel4->getCachedValues()['counters']['photos']);
        $this->assertSame(0, $albumLevel4->getCachedValues()['counters']['children']);
    }

    /*
   * When deleting a child:
   * - Decrease all old parents albums counters by the number of children album belonging to the child + 1 (itself)
   * - Decrease all old parents photos counters by the number of photos in the child and in all the child's children
   */
    public function test_delete_child_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel1, 'owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel2, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel3, 'owner' => $user]);
        $albumLevel4 = AlbumFactory::createOne(['parent' => $albumLevel3, 'owner' => $user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel4, 'owner' => $user]);

        // Act
        $albumLevel3->remove();
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(6, $albumLevel1->getCachedValues()['counters']['photos']);
        $this->assertSame(1, $albumLevel1->getCachedValues()['counters']['children']);

        $this->assertSame(3, $albumLevel2->getCachedValues()['counters']['photos']);
        $this->assertSame(0, $albumLevel2->getCachedValues()['counters']['children']);
    }

    /*
     * When adding a new photo, all parent counters must be increased by 1
     */
    public function test_add_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);
        PhotoFactory::createOne(['album' => $albumLevel3, 'owner' => $user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(1, $albumLevel1->getCachedValues()['counters']['photos']);
        $this->assertSame(1, $albumLevel2->getCachedValues()['counters']['photos']);
        $this->assertSame(1, $albumLevel3->getCachedValues()['counters']['photos']);
    }

    /*
     * When moving a photo, all parent new counters must be increased by 1 and old parent counters decreased by 1
     */
    public function test_move_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);
        $photo = PhotoFactory::createOne(['album' => $albumLevel3, 'owner' => $user]);

        // Act
        $newAlbum = AlbumFactory::createOne(['owner' => $user]);
        $photo->setAlbum($newAlbum->object());
        $photo->save();

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(1, $newAlbum->getCachedValues()['counters']['photos']);
        $this->assertSame(0, $albumLevel1->getCachedValues()['counters']['photos']);
        $this->assertSame(0, $albumLevel2->getCachedValues()['counters']['photos']);
        $this->assertSame(0, $albumLevel3->getCachedValues()['counters']['photos']);
    }

    /*
     * When deleting an photo decrease all old parents albums counters by one
     */
    public function test_delete_photo(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $user]);
        $photo = PhotoFactory::createOne(['album' => $albumLevel3, 'owner' => $user]);

        // Act
        $photo->remove();
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(0, $albumLevel1->getCachedValues()['counters']['photos']);
        $this->assertSame(0, $albumLevel2->getCachedValues()['counters']['photos']);
        $this->assertSame(0, $albumLevel3->getCachedValues()['counters']['photos']);
    }
}
