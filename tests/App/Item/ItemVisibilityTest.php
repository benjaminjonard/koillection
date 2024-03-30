<?php

declare(strict_types=1);

namespace App\Tests\App\Item;

use App\Enum\VisibilityEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ItemVisibilityTest extends AppTestCase
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
    public function test_visibility_add_item(string $collection1Visibility, string $collection2Visibility, string $itemFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user, 'visibility' => $collection1Visibility]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection2Visibility]);

        // Act
        $item = ItemFactory::createOne(['owner' => $user, 'collection' => $collectionLevel2]);

        // Assert
        ItemFactory::assert()->exists(['id' => $item->getId(), 'finalVisibility' => $itemFinalVisibility]);
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
    public function test_visibility_change_item_collection(string $collection1Visibility, string $collection2Visibility, string $itemFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $oldCollection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $oldCollection, 'owner' => $user]);
        $datum = DatumFactory::createOne(['item' => $item, 'owner' => $user]);

        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user, 'visibility' => $collection1Visibility]);
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection2Visibility]);

        // Act
        $item->setCollection($collectionLevel2->object());
        $item->save();

        // Assert
        ItemFactory::assert()->exists(['id' => $item->getId(), 'finalVisibility' => $itemFinalVisibility]);
        DatumFactory::assert()->exists(['id' => $datum->getId(), 'finalVisibility' => $itemFinalVisibility]);
    }

    #[TestWith(['public', true])]
    #[TestWith(['internal', false])]
    #[TestWith(['private', false])]
    public function test_shared_get_item_with_anonymous(string $visibility, bool $shouldSucceed): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        $item = ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => $visibility]);

        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/items/{$item->getId()}");

        // Assert
        if ($shouldSucceed) {
            $this->assertResponseIsSuccessful();
            $this->assertEquals($item->getName(), $crawler->filter('h1')->text());
            $this->assertCount(1, $crawler->filter('.datum-row'));
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    #[TestWith(['public', true])]
    #[TestWith(['internal', true])]
    #[TestWith(['private', false])]
    public function test_shared_get_item_with_other_user_logged(string $visibility, bool $shouldSucceed): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        $item = ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => $visibility]);

        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $otherUser = UserFactory::createOne()->object();
        $this->client->loginUser($otherUser);
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/items/{$item->getId()}");

        // Assert
        if ($shouldSucceed) {
            $this->assertResponseIsSuccessful();
            $this->assertEquals($item->getName(), $crawler->filter('h1')->text());
            $this->assertCount(2, $crawler->filter('.datum-row'));
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    #[TestWith(['public'])]
    #[TestWith(['internal'])]
    #[TestWith(['private'])]
    public function test_shared_get_item_with_owner_logged(string $visibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        $item = ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => $visibility]);

        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', "/items/{$item->getId()}");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals($item->getName(), $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.datum-row'));
    }
}
