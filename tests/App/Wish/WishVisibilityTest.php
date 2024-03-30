<?php

declare(strict_types=1);

namespace App\Tests\App\Wish;

use App\Tests\AppTestCase;
use App\Tests\Factory\WishlistFactory;
use App\Tests\Factory\WishFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\TestWith;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WishVisibilityTest extends AppTestCase
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
    public function test_visibility_add_wish(string $wishlist1Visibility, string $wishlist2Visibility, string $wishFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlistLevel1 = WishlistFactory::createOne(['owner' => $user, 'visibility' => $wishlist1Visibility]);
        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user, 'visibility' => $wishlist2Visibility]);

        // Act
        $wish = WishFactory::createOne(['owner' => $user, 'wishlist' => $wishlistLevel2]);

        // Assert
        WishFactory::assert()->exists(['id' => $wish->getId(), 'finalVisibility' => $wishFinalVisibility]);
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
    public function test_visibility_change_wish_wishlist(string $wishlist1Visibility, string $wishlist2Visibility, string $wishFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $oldWishlist = WishlistFactory::createOne(['owner' => $user]);
        $wish = WishFactory::createOne(['wishlist' => $oldWishlist, 'owner' => $user]);

        $wishlistLevel1 = WishlistFactory::createOne(['owner' => $user, 'visibility' => $wishlist1Visibility]);
        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user, 'visibility' => $wishlist2Visibility]);

        // Act
        $wish->setWishlist($wishlistLevel2->object());
        $wish->save();

        // Assert
        WishFactory::assert()->exists(['id' => $wish->getId(), 'finalVisibility' => $wishFinalVisibility]);
    }
}
