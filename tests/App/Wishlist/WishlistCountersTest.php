<?php

declare(strict_types=1);

namespace App\Tests\App\Wishlist;

use App\Service\CachedValuesGetter;
use App\Service\RefreshCachedValuesQueue;
use App\Tests\AppTestCase;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\WishFactory;
use App\Tests\Factory\WishlistFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WishlistCountersTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    public ?RefreshCachedValuesQueue $refreshCachedValuesQueue;
    public ?CachedValuesGetter $cachedValuesGetter;

    protected function setUp(): void
    {
        $this->refreshCachedValuesQueue = $this->getContainer()->get(RefreshCachedValuesQueue::class);
        $this->cachedValuesGetter = $this->getContainer()->get(CachedValuesGetter::class);
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
        $this->assertSame(2, $this->cachedValuesGetter->getCachedValues($wishlistLevel1->object())['counters']['children']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($wishlistLevel2->object())['counters']['children']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($wishlistLevel3->object())['counters']['children']);
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
        $newParentWishlist = WishlistFactory::createOne(['owner' => $user]);
        $wishlistLevel3->setParent($newParentWishlist->object());
        $wishlistLevel3->save();

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($newParentWishlist->object())['counters']['wishes']);
        $this->assertSame(2, $this->cachedValuesGetter->getCachedValues($newParentWishlist->object())['counters']['children']);

        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($wishlistLevel1->object())['counters']['wishes']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($wishlistLevel1->object())['counters']['children']);

        $this->assertSame(3, $this->cachedValuesGetter->getCachedValues($wishlistLevel2->object())['counters']['wishes']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($wishlistLevel2->object())['counters']['children']);

        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($wishlistLevel3->object())['counters']['wishes']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($wishlistLevel3->object())['counters']['children']);

        $this->assertSame(3, $this->cachedValuesGetter->getCachedValues($wishlistLevel4->object())['counters']['wishes']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($wishlistLevel4->object())['counters']['children']);
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
        $this->assertSame(6, $this->cachedValuesGetter->getCachedValues($wishlistLevel1->object())['counters']['wishes']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($wishlistLevel1->object())['counters']['children']);

        $this->assertSame(3, $this->cachedValuesGetter->getCachedValues($wishlistLevel2->object())['counters']['wishes']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($wishlistLevel2->object())['counters']['children']);
    }

    /*
     * When adding a new wish, all parent counters must be increased by 1
     */
    public function test_add_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlistLevel1 = WishlistFactory::createOne(['owner' => $user]);
        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user]);
        $wishlistLevel3 = WishlistFactory::createOne(['parent' => $wishlistLevel2, 'owner' => $user]);
        WishFactory::createOne(['wishlist' => $wishlistLevel3, 'owner' => $user]);

        // Act
        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($wishlistLevel1->object())['counters']['wishes']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($wishlistLevel2->object())['counters']['wishes']);
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($wishlistLevel3->object())['counters']['wishes']);
    }

    /*
     * When moving a wish, all parent new counters must be increased by 1 and old parent counters decreased by 1
     */
    public function test_move_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlistLevel1 = WishlistFactory::createOne(['owner' => $user]);
        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user]);
        $wishlistLevel3 = WishlistFactory::createOne(['parent' => $wishlistLevel2, 'owner' => $user]);
        $wish = WishFactory::createOne(['wishlist' => $wishlistLevel3, 'owner' => $user]);

        // Act
        $newWishlist = WishlistFactory::createOne(['owner' => $user]);
        $wish->setWishlist($newWishlist->object());
        $wish->save();

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(1, $this->cachedValuesGetter->getCachedValues($newWishlist->object())['counters']['wishes']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($wishlistLevel1->object())['counters']['wishes']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($wishlistLevel2->object())['counters']['wishes']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($wishlistLevel3->object())['counters']['wishes']);
    }

    /*
     * When deleting a wish decrease all old parents wishlists counters by one
     */
    public function test_delete_wish(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlistLevel1 = WishlistFactory::createOne(['owner' => $user]);
        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user]);
        $wishlistLevel3 = WishlistFactory::createOne(['parent' => $wishlistLevel2, 'owner' => $user]);
        $wish = WishFactory::createOne(['wishlist' => $wishlistLevel3, 'owner' => $user]);

        // Act
        $wish->remove();

        $this->refreshCachedValuesQueue->process();

        // Assert
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($wishlistLevel1->object())['counters']['wishes']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($wishlistLevel2->object())['counters']['wishes']);
        $this->assertSame(0, $this->cachedValuesGetter->getCachedValues($wishlistLevel3->object())['counters']['wishes']);
    }
}
