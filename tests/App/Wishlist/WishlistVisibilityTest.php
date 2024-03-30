<?php

declare(strict_types=1);

namespace App\Tests\App\Wishlist;

use App\Enum\VisibilityEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\WishlistFactory;
use App\Tests\Factory\WishFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WishlistVisibilityTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
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
    public function test_visibility_add_nested_wishlist(string $wishlist1Visibility, string $wishlist3Visibility, string $wishlist2FinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlistLevel1 = WishlistFactory::createOne(['owner' => $user, 'visibility' => $wishlist1Visibility]);

        // Act
        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user, 'visibility' => $wishlist3Visibility]);

        // Assert
        WishlistFactory::assert()->exists(['id' => $wishlistLevel2->getId(), 'finalVisibility' => $wishlist2FinalVisibility]);
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
    public function test_visibility_change_parent_wishlist(string $newWishlistVisibility, string $wishlist2Visibility, string $wishlist1FinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlistLevel1 = WishlistFactory::createOne(['parent' => null, 'owner' => $user]);

        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user, 'visibility' => $wishlist2Visibility]);
        $wish2 = WishFactory::createOne(['wishlist' => $wishlistLevel2, 'owner' => $user]);

        // Act
        $newParentWishlist = WishlistFactory::createOne(['owner' => $user, 'visibility' => $newWishlistVisibility]);
        $wishlistLevel2->setParent($newParentWishlist->object());
        $wishlistLevel2->save();

        // Assert
        WishlistFactory::assert()->exists(['id' => $wishlistLevel2->getId(), 'finalVisibility' => $wishlist1FinalVisibility]);
        WishFactory::assert()->exists(['id' => $wish2->getId(), 'finalVisibility' => $wishlist1FinalVisibility]);
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
    public function test_visibility_change_wishlist_visibility(string $wishlist1Visibility, string $wishlist2Visibility, string $level1Visibility, string $level2Visibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlistLevel1 = WishlistFactory::createOne(['parent' => null, 'owner' => $user]);
        $wish1 = WishFactory::createOne(['wishlist' => $wishlistLevel1, 'owner' => $user]);

        $wishlistLevel2 = WishlistFactory::createOne(['parent' => $wishlistLevel1, 'owner' => $user, 'visibility' => $wishlist2Visibility]);
        $wish2 = WishFactory::createOne(['wishlist' => $wishlistLevel2, 'owner' => $user]);

        // Act
        $wishlistLevel1->setVisibility($wishlist1Visibility);
        $wishlistLevel1->save();

        // Assert
        WishlistFactory::assert()->exists(['id' => $wishlistLevel1->getId(), 'finalVisibility' => $level1Visibility]);
        WishFactory::assert()->exists(['id' => $wish1->getId(), 'finalVisibility' => $level1Visibility]);
        WishlistFactory::assert()->exists(['id' => $wishlistLevel2->getId(), 'finalVisibility' => $level2Visibility]);
        WishFactory::assert()->exists(['id' => $wish2->getId(), 'finalVisibility' => $level2Visibility]);
    }

    public function test_shared_wishlists_list_with_anonymous(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        WishlistFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        WishlistFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        WishlistFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/wishlists");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Wishlists', $crawler->filter('h1')->text());
        $this->assertCount(1, $crawler->filter('.collection-element'));
    }

    public function test_shared_wishlists_list_with_other_user_logged(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        WishlistFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        WishlistFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        WishlistFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        $otherUser = UserFactory::createOne()->object();
        $this->client->loginUser($otherUser);

        // Act
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/wishlists");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Wishlists', $crawler->filter('h1')->text());
        $this->assertCount(2, $crawler->filter('.collection-element'));
    }

    public function test_shared_wishlists_list_with_owner_logged(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        WishlistFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        WishlistFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        WishlistFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', "/wishlists");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Wishlists', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
    }

    #[TestWith(['public', true])]
    #[TestWith(['internal', false])]
    #[TestWith(['private', false])]
    public function test_shared_get_wishlist_with_anonymous(string $visibility, bool $shouldSucceed): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user, 'visibility' => $visibility]);

        WishlistFactory::createOne(['owner' => $user, 'parent' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        WishlistFactory::createOne(['owner' => $user, 'parent' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        WishlistFactory::createOne(['owner' => $user, 'parent' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        WishFactory::createOne(['owner' => $user, 'wishlist' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        WishFactory::createOne(['owner' => $user, 'wishlist' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        WishFactory::createOne(['owner' => $user, 'wishlist' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->request('GET', "/user/{$user->getUsername()}/wishlists"); //Don't know why it's needed, it seems like $wishlist isn't properly initialized, maybe from some cache
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/wishlists/{$wishlist->getId()}");

        // Assert
        if ($shouldSucceed) {
            $this->assertResponseIsSuccessful();
            $this->assertEquals($wishlist->getName(), $crawler->filter('h1')->text());
            $this->assertCount(1, $crawler->filter('.collection-element'));
            $this->assertCount(1, $crawler->filter('.list-element'));
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    #[TestWith(['public', true])]
    #[TestWith(['internal', true])]
    #[TestWith(['private', false])]
    public function test_shared_get_wishlist_with_other_logged_user(string $visibility, bool $shouldSucceed): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user, 'visibility' => $visibility]);

        WishlistFactory::createOne(['owner' => $user, 'parent' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        WishlistFactory::createOne(['owner' => $user, 'parent' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        WishlistFactory::createOne(['owner' => $user, 'parent' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        WishFactory::createOne(['owner' => $user, 'wishlist' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        WishFactory::createOne(['owner' => $user, 'wishlist' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        WishFactory::createOne(['owner' => $user, 'wishlist' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $otherUser = UserFactory::createOne()->object();
        $this->client->loginUser($otherUser);
        $this->client->request('GET', "/user/{$user->getUsername()}/wishlists"); //Don't know why it's needed, it seems like $wishlist isn't properly initialized, maybe from some cache
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/wishlists/{$wishlist->getId()}");

        // Assert
        if ($shouldSucceed) {
            $this->assertResponseIsSuccessful();
            $this->assertEquals($wishlist->getName(), $crawler->filter('h1')->text());
            $this->assertCount(2, $crawler->filter('.collection-element'));
            $this->assertCount(2, $crawler->filter('.list-element'));
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    #[TestWith(['public'])]
    #[TestWith(['internal'])]
    #[TestWith(['private'])]
    public function test_get_wishlist_with_owner_logged(string $visibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $wishlist = WishlistFactory::createOne(['owner' => $user, 'visibility' => $visibility]);

        WishlistFactory::createOne(['owner' => $user, 'parent' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        WishlistFactory::createOne(['owner' => $user, 'parent' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        WishlistFactory::createOne(['owner' => $user, 'parent' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        WishFactory::createOne(['owner' => $user, 'wishlist' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        WishFactory::createOne(['owner' => $user, 'wishlist' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        WishFactory::createOne(['owner' => $user, 'wishlist' => $wishlist, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', "/wishlists/{$wishlist->getId()}");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals($wishlist->getName(), $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
        $this->assertCount(3, $crawler->filter('.list-element'));
    }
}
