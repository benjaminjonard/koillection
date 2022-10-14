<?php

declare(strict_types=1);

namespace App\Tests\Api\Datum;

use App\Factory\CollectionFactory;
use App\Factory\DatumFactory;
use App\Factory\ItemFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class DatumApiNotOwnerTest extends ApiTestCase
{
    use Factories;

    public function test_cant_get_another_user_datum(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/data/'.$datum->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_datum_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $owner]);
        $datum = DatumFactory::createOne(['item' => $item, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/data/'.$datum->getId().'/item');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_datum_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/data/'.$datum->getId().'/collection');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_post_datum_with_another_user_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/datum/', ['json' => [
            'collection' => '/api/collections/'.$collection->getId()
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_post_datum_with_another_user_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/datum/', ['json' => [
            'item' => '/api/items/'.$item->getId()
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_put_another_user_datum(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/data/'.$datum->getId(), ['json' => [
            'label' => 'Author',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_datum(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/data/'.$datum->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'label' => 'Author',
            ]
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_datum(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/data/'.$datum->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
