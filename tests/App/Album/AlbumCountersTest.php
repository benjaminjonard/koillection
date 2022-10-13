<?php

declare(strict_types=1);

namespace App\Tests\App\Album;

use App\Enum\RoleEnum;
use App\Factory\AlbumFactory;
use App\Factory\PhotoFactory;
use App\Factory\UserFactory;
use App\Service\RefreshCachedValuesQueue;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class AlbumCountersTest extends WebTestCase
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
    public function test_add_child_album(): void
    {
        // Arrange
        $albumLevel1 = AlbumFactory::createOne(['owner' => $this->user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $this->user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $this->user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEquals(2, $albumLevel1->getCachedValues()['counters']['children']);
        $this->assertEquals(1, $albumLevel2->getCachedValues()['counters']['children']);
        $this->assertEquals(0, $albumLevel3->getCachedValues()['counters']['children']);
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
        $albumLevel1 = AlbumFactory::createOne(['parent' => null, 'owner' => $this->user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel1, 'owner' => $this->user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $this->user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel2, 'owner' => $this->user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $this->user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel3, 'owner' => $this->user]);
        $albumLevel4 = AlbumFactory::createOne(['parent' => $albumLevel3, 'owner' => $this->user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel4, 'owner' => $this->user]);

        // Act
        $newParentAlbum = AlbumFactory::createOne(['owner' => $this->user]);
        $albumLevel3->setParent($newParentAlbum->object());
        $albumLevel3->save();
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEquals(6, $newParentAlbum->getCachedValues()['counters']['photos']);
        $this->assertEquals(2, $newParentAlbum->getCachedValues()['counters']['children']);

        $this->assertEquals(6, $albumLevel1->getCachedValues()['counters']['photos']);
        $this->assertEquals(1, $albumLevel1->getCachedValues()['counters']['children']);

        $this->assertEquals(3, $albumLevel2->getCachedValues()['counters']['photos']);
        $this->assertEquals(0, $albumLevel2->getCachedValues()['counters']['children']);

        $this->assertEquals(6, $albumLevel3->getCachedValues()['counters']['photos']);
        $this->assertEquals(1, $albumLevel3->getCachedValues()['counters']['children']);

        $this->assertEquals(3, $albumLevel4->getCachedValues()['counters']['photos']);
        $this->assertEquals(0, $albumLevel4->getCachedValues()['counters']['children']);
    }

    /*
   * When deleting a child:
   * - Decrease all old parents albums counters by the number of children album belonging to the child + 1 (itself)
   * - Decrease all old parents photos counters by the number of photos in the child and in all the child's children
   */
    public function test_delete_child_album(): void
    {
        // Arrange
        $albumLevel1 = AlbumFactory::createOne(['owner' => $this->user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel1, 'owner' => $this->user]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $this->user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel2, 'owner' => $this->user]);
        $albumLevel3 = AlbumFactory::createOne(['parent' => $albumLevel2, 'owner' => $this->user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel3, 'owner' => $this->user]);
        $albumLevel4 = AlbumFactory::createOne(['parent' => $albumLevel3, 'owner' => $this->user]);
        PhotoFactory::createMany(3, ['album' => $albumLevel4, 'owner' => $this->user]);

        // Act
        $albumLevel3->remove();
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertEquals(6, $albumLevel1->getCachedValues()['counters']['photos']);
        $this->assertEquals(1, $albumLevel1->getCachedValues()['counters']['children']);

        $this->assertEquals(3, $albumLevel2->getCachedValues()['counters']['photos']);
        $this->assertEquals(0, $albumLevel2->getCachedValues()['counters']['children']);
    }
}
