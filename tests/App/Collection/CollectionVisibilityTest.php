<?php

declare(strict_types=1);

namespace App\Tests\App\Collection;

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

class CollectionVisibilityTest extends AppTestCase
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
    public function test_visibility_add_nested_collection(string $collection1Visibility, string $collection3Visibility, string $collection2FinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['owner' => $user, 'visibility' => $collection1Visibility]);

        // Act
        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection3Visibility]);

        // Assert
        CollectionFactory::assert()->exists(['id' => $collectionLevel2->getId(), 'finalVisibility' => $collection2FinalVisibility]);
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
    public function test_visibility_change_parent_collection(string $newCollectionVisibility, string $collection2Visibility, string $collection1FinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection2Visibility]);
        $item2 = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        $datumCollection2 = DatumFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        $datumItem2 = DatumFactory::createOne(['item' => $item2, 'owner' => $user]);

        // Act
        $newParentCollection = CollectionFactory::createOne(['owner' => $user, 'visibility' => $newCollectionVisibility]);
        $collectionLevel2->setParent($newParentCollection->object());
        $collectionLevel2->save();

        // Assert
        CollectionFactory::assert()->exists(['id' => $collectionLevel2->getId(), 'finalVisibility' => $collection1FinalVisibility]);
        DatumFactory::assert()->exists(['id' => $datumCollection2->getId(), 'finalVisibility' => $collection1FinalVisibility]);
        ItemFactory::assert()->exists(['id' => $item2->getId(), 'finalVisibility' => $collection1FinalVisibility]);
        DatumFactory::assert()->exists(['id' => $datumItem2->getId(), 'finalVisibility' => $collection1FinalVisibility]);
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
    public function test_visibility_change_collection_visibility(string $collection1Visibility, string $collection2Visibility, string $level1Visibility, string $level2Visibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collectionLevel1 = CollectionFactory::createOne(['parent' => null, 'owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collectionLevel1, 'owner' => $user]);
        $datumCollection1 = DatumFactory::createOne(['collection' => $collectionLevel1, 'owner' => $user]);
        $datumItem1 = DatumFactory::createOne(['item' => $item1, 'owner' => $user]);

        $collectionLevel2 = CollectionFactory::createOne(['parent' => $collectionLevel1, 'owner' => $user, 'visibility' => $collection2Visibility]);
        $item2 = ItemFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        $datumCollection2 = DatumFactory::createOne(['collection' => $collectionLevel2, 'owner' => $user]);
        $datumItem2 = DatumFactory::createOne(['item' => $item2, 'owner' => $user]);

        // Act
        $collectionLevel1->setVisibility($collection1Visibility);
        $collectionLevel1->save();

        // Assert
        CollectionFactory::assert()->exists(['id' => $collectionLevel1->getId(), 'finalVisibility' => $level1Visibility]);
        ItemFactory::assert()->exists(['id' => $item1->getId(), 'finalVisibility' => $level1Visibility]);
        DatumFactory::assert()->exists(['id' => $datumCollection1->getId(), 'finalVisibility' => $level1Visibility]);
        DatumFactory::assert()->exists(['id' => $datumItem1->getId(), 'finalVisibility' => $level1Visibility]);

        CollectionFactory::assert()->exists(['id' => $collectionLevel2->getId(), 'finalVisibility' => $level2Visibility]);
        ItemFactory::assert()->exists(['id' => $item2->getId(), 'finalVisibility' => $level2Visibility]);
        DatumFactory::assert()->exists(['id' => $datumCollection2->getId(), 'finalVisibility' => $level2Visibility]);
        DatumFactory::assert()->exists(['id' => $datumItem2->getId(), 'finalVisibility' => $level2Visibility]);
    }

    public function test_shared_collections_list_with_anonymous(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/collections");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Collections', $crawler->filter('h1')->text());
        $this->assertCount(1, $crawler->filter('.collection-element'));
    }

    public function test_shared_collections_list_with_other_user_logged(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        $otherUser = UserFactory::createOne()->object();
        $this->client->loginUser($otherUser);

        // Act
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/collections");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Collections', $crawler->filter('h1')->text());
        $this->assertCount(2, $crawler->filter('.collection-element'));
    }

    public function test_shared_collections_list_with_owner_logged(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', "/collections");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Collections', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
    }

    #[TestWith(['public', true])]
    #[TestWith(['internal', false])]
    #[TestWith(['private', false])]
    public function test_shared_get_collection_with_anonymous(string $visibility, bool $shouldSucceed): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => $visibility]);

        CollectionFactory::createOne(['owner' => $user, 'parent' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        CollectionFactory::createOne(['owner' => $user, 'parent' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        CollectionFactory::createOne(['owner' => $user, 'parent' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->request('GET', "/user/{$user->getUsername()}/collections"); //Don't know why it's needed, it seems like $collection isn't properly initialized, maybe from some cache
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/collections/{$collection->getId()}");

        // Assert
        if ($shouldSucceed) {
            $this->assertResponseIsSuccessful();
            $this->assertEquals($collection->getTitle(), $crawler->filter('h1')->text());
            $this->assertCount(1, $crawler->filter('.collection-element'));
            $this->assertCount(1, $crawler->filter('.collection-item'));
            $this->assertCount(1, $crawler->filter('.datum-row'));
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    #[TestWith(['public', true])]
    #[TestWith(['internal', true])]
    #[TestWith(['private', false])]
    public function test_shared_get_collection_with_other_user_logged(string $visibility, bool $shouldSucceed): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => $visibility]);

        CollectionFactory::createOne(['owner' => $user, 'parent' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        CollectionFactory::createOne(['owner' => $user, 'parent' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        CollectionFactory::createOne(['owner' => $user, 'parent' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $otherUser = UserFactory::createOne()->object();
        $this->client->loginUser($otherUser);
        $this->client->request('GET', "/user/{$user->getUsername()}/collections"); //Don't know why it's needed, it seems like $collection isn't properly initialized, maybe from some cache
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/collections/{$collection->getId()}");

        // Assert
        if ($shouldSucceed) {
            $this->assertResponseIsSuccessful();
            $this->assertEquals($collection->getTitle(), $crawler->filter('h1')->text());
            $this->assertCount(2, $crawler->filter('.collection-element'));
            $this->assertCount(2, $crawler->filter('.collection-item'));
            $this->assertCount(2, $crawler->filter('.datum-row'));
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    #[TestWith(['public'])]
    #[TestWith(['internal'])]
    #[TestWith(['private'])]
    public function test_get_collection_with_owner_logged(string $visibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => $visibility]);

        CollectionFactory::createOne(['owner' => $user, 'parent' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        CollectionFactory::createOne(['owner' => $user, 'parent' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        CollectionFactory::createOne(['owner' => $user, 'parent' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', "/collections/{$collection->getId()}");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals($collection->getTitle(), $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
        $this->assertCount(3, $crawler->filter('.collection-item'));
        $this->assertCount(3, $crawler->filter('.datum-row'));
    }
}
