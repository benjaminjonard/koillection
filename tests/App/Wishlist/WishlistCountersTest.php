<?php

declare(strict_types=1);

namespace App\Tests\App\Wishlist;

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Factory\WishlistFactory;
use App\Factory\WishFactory;
use App\Factory\UserFactory;
use App\Service\RefreshCachedValuesQueue;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class WishlistCountersTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->refreshCachedValuesQueue = $this->getContainer()->get(RefreshCachedValuesQueue::class);
    }

    /*
     * When adding a new child, all parent counters must be increased by 1
     */
    public function test_add_child_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlistLevel1 = WishlistFactory::createOne(['owner' => $user]);
        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user]);
        $wishlistLevel3 = WishlistFactory::createOne(['parent' => $wishlistLevel2, 'owner' => $user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(2, $wishlistLevel1->getCachedValues()['counters']['children']);
        $this->assertSame(1, $wishlistLevel2->getCachedValues()['counters']['children']);
        $this->assertSame(0, $wishlistLevel3->getCachedValues()['counters']['children']);
    }

    /*
     * When moving a child:
     * - Decrease all old parents wishlists counters by the number of children wishlist belonging to the child + 1 (itself)
     * - Decrease all old parents wishes counters by the number of wishes in the child and in all the child's children
     * - Increase all new parents wishlists counters by the number of children wishlist belonging to the child + 1 (itself)
     * - Increase all new parents wishes counters by the number of wishes in the child and in all the child's children
     */
    public function test_move_child_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlistLevel1 = WishlistFactory::createOne(['parent' => null, 'owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlistLevel1, 'owner' => $user]);
        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlistLevel2, 'owner' => $user]);
        $wishlistLevel3 = WishlistFactory::createOne(['parent' => $wishlistLevel2, 'owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlistLevel3, 'owner' => $user]);
        $wishlistLevel4 = WishlistFactory::createOne(['parent' => $wishlistLevel3, 'owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlistLevel4, 'owner' => $user]);

        // Act
        $newParentAlbum = WishlistFactory::createOne(['owner' => $user]);
        $wishlistLevel3->setParent($newParentAlbum->object());
        $wishlistLevel3->save();

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(6, $newParentAlbum->getCachedValues()['counters']['wishes']);
        $this->assertSame(2, $newParentAlbum->getCachedValues()['counters']['children']);

        $this->assertSame(6, $wishlistLevel1->getCachedValues()['counters']['wishes']);
        $this->assertSame(1, $wishlistLevel1->getCachedValues()['counters']['children']);

        $this->assertSame(3, $wishlistLevel2->getCachedValues()['counters']['wishes']);
        $this->assertSame(0, $wishlistLevel2->getCachedValues()['counters']['children']);

        $this->assertSame(6, $wishlistLevel3->getCachedValues()['counters']['wishes']);
        $this->assertSame(1, $wishlistLevel3->getCachedValues()['counters']['children']);

        $this->assertSame(3, $wishlistLevel4->getCachedValues()['counters']['wishes']);
        $this->assertSame(0, $wishlistLevel4->getCachedValues()['counters']['children']);
    }

    /*
   * When deleting a child:
   * - Decrease all old parents wishlists counters by the number of children wishlist belonging to the child + 1 (itself)
   * - Decrease all old parents wishes counters by the number of wishes in the child and in all the child's children
   */
    public function test_delete_child_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlistLevel1 = WishlistFactory::createOne(['owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlistLevel1, 'owner' => $user]);
        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlistLevel2, 'owner' => $user]);
        $wishlistLevel3 = WishlistFactory::createOne(['parent' => $wishlistLevel2, 'owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlistLevel3, 'owner' => $user]);
        $wishlistLevel4 = WishlistFactory::createOne(['parent' => $wishlistLevel3, 'owner' => $user]);
        WishFactory::createMany(3, ['wishlist' => $wishlistLevel4, 'owner' => $user]);

        // Act
        $wishlistLevel3->remove();
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(6, $wishlistLevel1->getCachedValues()['counters']['wishes']);
        $this->assertSame(1, $wishlistLevel1->getCachedValues()['counters']['children']);

        $this->assertSame(3, $wishlistLevel2->getCachedValues()['counters']['wishes']);
        $this->assertSame(0, $wishlistLevel2->getCachedValues()['counters']['children']);
    }
}
