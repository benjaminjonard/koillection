<?php

declare(strict_types=1);

namespace App\Tests\Api\Collection;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Tests\ApiTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CollectionApiNotOwnerTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_cant_get_another_user_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/collections/' . $collection->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_collection_children(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        CollectionFactory::createMany(3, ['parent' => $collection, 'owner' => $owner]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/collections/' . $collection->getId() . '/children');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['totalItems']);
        $this->assertCount(0, $data['member']);
        $this->assertMatchesResourceCollectionJsonSchema(Collection::class);
    }

    public function test_cant_get_another_user_collection_parent(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $parent = CollectionFactory::createOne(['owner' => $owner]);
        $collection = CollectionFactory::createOne(['parent' => $parent, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/collections/' . $collection->getId() . '/parent');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_collection_items(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        ItemFactory::createMany(3, ['collection' => $collection, 'owner' => $owner]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/collections/' . $collection->getId() . '/items');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['totalItems']);
        $this->assertCount(0, $data['member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function test_cant_get_another_user_collection_data(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        DatumFactory::createMany(3, ['collection' => $collection, 'owner' => $owner]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/collections/' . $collection->getId() . '/data');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['totalItems']);
        $this->assertCount(0, $data['member']);
        $this->assertMatchesResourceCollectionJsonSchema(Datum::class);
    }

    public function test_cant_post_collection_in_another_user_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/collections/', ['json' => [
            'parent' => '/api/collections/' . $collection,
            'name' => 'Berserk',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_post_collection_with_another_user_datum(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        CollectionFactory::createOne(['owner' => $user]);
        $owner = UserFactory::createOne()->_real();
        $datum = DatumFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/collections/', ['json' => [
            'name' => 'Berserk',
            'data' => [$datum]
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_put_another_user_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/collections/' . $collection->getId(), ['json' => [
            'title' => 'Berserk',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/collections/' . $collection->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'Berserk',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/collections/' . $collection->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
