<?php

declare(strict_types=1);

namespace App\Tests\Api\Datum;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Enum\DatumTypeEnum;
use App\Factory\CollectionFactory;
use App\Factory\DatumFactory;
use App\Factory\ItemFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class DatumApiTest extends ApiTestCase
{
    use Factories;

    public function test_get_data(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        DatumFactory::createMany(3, ['owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/data');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Datum::class);
    }

    public function test_get_datum(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $datum = DatumFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/data/'.$datum->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertJsonContains([
            'id' => $datum->getId()
        ]);
    }

    public function test_get_datum_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $datum = DatumFactory::createOne(['item' => $item, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/data/'.$datum->getId().'/item');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Item::class);
        $this->assertJsonContains([
            'id' => $item->getId()
        ]);
    }

    public function test_get_datum_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/data/'.$datum->getId().'/collection');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Collection::class);
        $this->assertJsonContains([
            'id' => $collection->getId()
        ]);
    }

    public function test_post_datum_with_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => [
            'collection' => '/api/collections/'.$collection->getId(),
            'label' => 'Japanese title',
            'value' => ' 葬送のフリーレン',
            'type' => DatumTypeEnum::TYPE_TEXT,
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertJsonContains([
            'collection' => '/api/collections/'.$collection->getId(),
        ]);
    }

    public function test_post_datum_with_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => [
            'item' => '/api/items/'.$item->getId(),
            'label' => 'Japanese title',
            'value' => ' 葬送のフリーレン',
            'type' => DatumTypeEnum::TYPE_TEXT,
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertJsonContains([
            'item' => '/api/items/'.$item->getId(),
        ]);
    }

    public function test_post_datum_with_collection_and_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => [
            'collection' => '/api/collections/'.$collection->getId(),
            'item' => '/api/items/'.$item->getId(),
            'label' => 'Japanese title',
            'value' => ' 葬送のフリーレン',
            'type' => DatumTypeEnum::TYPE_TEXT,
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonContains([
            'hydra:description' => $this->translator->trans('error.datum.cant_be_used_by_both_collections_and_items', [], 'validators'),
        ]);
    }

    public function test_post_datum_without_collection_nor_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => [
            'label' => 'Japanese title',
            'value' => ' 葬送のフリーレン',
            'type' => DatumTypeEnum::TYPE_TEXT,
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonContains([
            'hydra:description' => $this->translator->trans('error.datum.must_provide_collection_or_item', [], 'validators'),
        ]);
    }

    public function test_put_datum(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'label' => 'Title', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/data/'.$datum->getId(), ['json' => [
            'label' => 'Author',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertJsonContains([
            'id' => $datum->getId(),
            'label' => 'Author',
        ]);
    }

    public function test_patch_datum(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'label' => 'Title', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/data/'.$datum->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'label' => 'Author',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertJsonContains([
            'id' => $datum->getId(),
            'label' => 'Author',
        ]);
    }

    public function test_delete_datum(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $datum = DatumFactory::createOne(['label' => 'Title', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/data/'.$datum->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
