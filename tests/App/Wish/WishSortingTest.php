<?php

declare(strict_types=1);

namespace App\Tests\App\Wish;

use App\Tests\AppTestCase;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\WishFactory;
use App\Tests\Factory\WishlistFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WishSortingTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function test_new_wish_is_added_at_the_end_of_the_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        $otherWishlist = WishlistFactory::createOne(['owner' => $user]);
        WishFactory::createOne(['name' => 'Wish A', 'wishlist' => $otherWishlist, 'owner' => $user]);

        // Act
        $firstWish = WishFactory::createOne(['name' => 'Wish B', 'wishlist' => $wishlist, 'owner' => $user]);
        $secondWish = WishFactory::createOne(['name' => 'Wish A', 'wishlist' => $wishlist, 'owner' => $user]);

        // Assert
        $this->assertSame(1, $firstWish->getPosition());
        $this->assertSame(2, $secondWish->getPosition());
    }

    public function test_wishes_are_displayed_by_position(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        WishFactory::createOne(['name' => 'Wish A', 'wishlist' => $wishlist, 'owner' => $user, 'position' => 2]);
        WishFactory::createOne(['name' => 'Wish B', 'wishlist' => $wishlist, 'owner' => $user, 'position' => 1]);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/wishlists/' . $wishlist->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(['Wish B', 'Wish A'], $crawler->filter('tr.wish')->each(fn ($node) => $node->attr('data-title')));
    }

    public function test_can_sort_wishes(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        $wishA = WishFactory::createOne(['name' => 'Wish A', 'wishlist' => $wishlist, 'owner' => $user]);
        $wishB = WishFactory::createOne(['name' => 'Wish B', 'wishlist' => $wishlist, 'owner' => $user]);
        $wishC = WishFactory::createOne(['name' => 'Wish C', 'wishlist' => $wishlist, 'owner' => $user]);

        // Act
        $this->client->request(Request::METHOD_POST, '/wishlists/' . $wishlist->getId() . '/sort-wishes', [
            'wishes' => [$wishC->getId(), $wishA->getId(), $wishB->getId()],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(204);
        WishFactory::assert()->exists(['id' => $wishC->getId(), 'position' => 1]);
        WishFactory::assert()->exists(['id' => $wishA->getId(), 'position' => 2]);
        WishFactory::assert()->exists(['id' => $wishB->getId(), 'position' => 3]);
    }

    public function test_sort_ignores_wishes_from_another_wishlist(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $wishlist = WishlistFactory::createOne(['owner' => $user]);
        $otherWishlist = WishlistFactory::createOne(['owner' => $user]);
        $wish = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $user]);
        $otherWish = WishFactory::createOne(['wishlist' => $otherWishlist, 'owner' => $user]);

        // Act
        $this->client->request(Request::METHOD_POST, '/wishlists/' . $wishlist->getId() . '/sort-wishes', [
            'wishes' => [$otherWish->getId(), $wish->getId()],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(204);
        WishFactory::assert()->exists(['id' => $wish->getId(), 'position' => 1]);
        WishFactory::assert()->exists(['id' => $otherWish->getId(), 'position' => 1]);
    }

    public function test_cant_sort_wishes_of_another_user(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $otherUser = UserFactory::createOne();
        $this->client->loginUser($user);
        $wishlist = WishlistFactory::createOne(['owner' => $otherUser]);
        $wishA = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $otherUser]);
        $wishB = WishFactory::createOne(['wishlist' => $wishlist, 'owner' => $otherUser]);

        // Act
        $this->client->request(Request::METHOD_POST, '/wishlists/' . $wishlist->getId() . '/sort-wishes', [
            'wishes' => [$wishB->getId(), $wishA->getId()],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(404);
        WishFactory::assert()->exists(['id' => $wishA->getId(), 'position' => 1]);
        WishFactory::assert()->exists(['id' => $wishB->getId(), 'position' => 2]);
    }
}
