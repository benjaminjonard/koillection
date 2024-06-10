<?php

declare(strict_types=1);

namespace App\Tests\Api\Datum;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Enum\DatumTypeEnum;
use App\Tests\ApiTestCase;
use App\Tests\Factory\ChoiceListFactory;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DatumApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_get_data(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
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
        $user = UserFactory::createOne()->_real();
        $datum = DatumFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/data/' . $datum->getId());

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
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $datum = DatumFactory::createOne(['item' => $item, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/data/' . $datum->getId() . '/item');

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
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/data/' . $datum->getId() . '/collection');

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
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => [
            'collection' => '/api/collections/' . $collection->getId(),
            'label' => 'Japanese title',
            'value' => ' 葬送のフリーレン',
            'type' => DatumTypeEnum::TYPE_TEXT,
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertJsonContains([
            'collection' => '/api/collections/' . $collection->getId(),
        ]);
    }

    public function test_post_datum_with_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => [
            'item' => '/api/items/' . $item->getId(),
            'label' => 'Japanese title',
            'value' => ' 葬送のフリーレン',
            'type' => DatumTypeEnum::TYPE_TEXT,
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertJsonContains([
            'item' => '/api/items/' . $item->getId(),
        ]);
    }

    public function test_post_datum_with_collection_and_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => [
            'collection' => '/api/collections/' . $collection->getId(),
            'item' => '/api/items/' . $item->getId(),
            'label' => 'Japanese title',
            'value' => ' 葬送のフリーレン',
            'type' => DatumTypeEnum::TYPE_TEXT,
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonContains([
            'hydra:description' => 'A datum cannot be used with both item and collection'
        ]);
    }

    public function test_post_datum_without_collection_nor_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => [
            'label' => 'Japanese title',
            'value' => ' 葬送のフリーレン',
            'type' => DatumTypeEnum::TYPE_TEXT,
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonContains([
            'hydra:description' => 'A collection or an item must be provided',
        ]);
    }

    public function test_put_datum(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'label' => 'Title', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/data/' . $datum->getId(), ['json' => [
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
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'label' => 'Title', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/data/' . $datum->getId(), [
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
        $user = UserFactory::createOne()->_real();
        $datum = DatumFactory::createOne(['label' => 'Title', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/data/' . $datum->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function test_post_datum_image(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $uploadedFile = $this->createFile('png');

        // Act
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/data/' . $datum->getId() . '/image', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'fileImage' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['image']);
        $this->assertFileExists(json_decode($crawler->getContent(), true)['image']);
    }

    public function test_post_datum_file(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $uploadedFile = $this->createFile('png');

        // Act
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/data/' . $datum->getId() . '/file', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'fileFile' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['file']);
        $this->assertFileExists(json_decode($crawler->getContent(), true)['file']);
    }

    public function test_post_datum_video(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $uploadedFile = $this->createFile('mp4');

        // Act
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/data/' . $datum->getId() . '/video', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'fileVideo' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['video']);
        $this->assertFileExists(json_decode($crawler->getContent(), true)['video']);
    }

    public function test_post_datum_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $choiceList = ChoiceListFactory::createOne(['name' => 'Progress', 'choices' => ['New', 'In progress', 'Done'], 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => [
            'item' => '/api/items/' . $item->getId(),
            'label' => 'Progress',
            'value' => '["New"]',
            'type' => DatumTypeEnum::TYPE_CHOICE_LIST,
            'choiceList' => '/api/choice_lists/' . $choiceList->getId()
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertJsonContains([
            'item' => '/api/items/' . $item->getId(),
            'choiceList' => '/api/choice_lists/' . $choiceList->getId(),
        ]);
    }

    public function test_cant_post_datum_choice_list_without_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => [
            'item' => '/api/items/' . $item->getId(),
            'label' => 'Progress',
            'value' => 'New',
            'type' => DatumTypeEnum::TYPE_CHOICE_LIST
        ]]);

        // Assert
        $this->assertResponseIsUnprocessable();
    }

    #[TestWith([DatumTypeEnum::TYPE_TEXT, 'Test value'])]
    #[TestWith([DatumTypeEnum::TYPE_TEXTAREA, 'Test value'])]
    #[TestWith([DatumTypeEnum::TYPE_LINK, 'https://fr.wikipedia.org'])]
    #[TestWith([DatumTypeEnum::TYPE_PRICE, '8.50'])]
    #[TestWith([DatumTypeEnum::TYPE_COUNTRY, 'FR'])]
    #[TestWith([DatumTypeEnum::TYPE_DATE, '2024-06-10'])]
    #[TestWith([DatumTypeEnum::TYPE_RATING, '7'])]
    #[TestWith([DatumTypeEnum::TYPE_NUMBER, '-8.50'])]
    #[TestWith([DatumTypeEnum::TYPE_LIST, '["Value 1", "Value 2"]'])]
    #[TestWith([DatumTypeEnum::TYPE_CHOICE_LIST, '["Value 1", "Value 2"]'])]
    #[TestWith([DatumTypeEnum::TYPE_CHECKBOX, '1'])]
    #[TestWith([DatumTypeEnum::TYPE_IMAGE, null])]
    #[TestWith([DatumTypeEnum::TYPE_SIGN, null])]
    #[TestWith([DatumTypeEnum::TYPE_FILE, null])]
    #[TestWith([DatumTypeEnum::TYPE_VIDEO, null])]
    public function test_datum_format_ok(string $type, ?string $value): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        $payload = [
            'item' => '/api/items/' . $item->getId(),
            'label' => 'Test label',
            'value' => $value,
            'type' => $type,
        ];

        if ($type === DatumTypeEnum::TYPE_CHOICE_LIST) {
            $choiceList = ChoiceListFactory::createOne(['name' => 'Progress', 'choices' => ['Value 1', 'Value 2', 'Value 3'], 'owner' => $user]);
            $payload['choiceList'] = '/api/choice_lists/' . $choiceList->getId();
        }

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => $payload]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Datum::class);
        $this->assertJsonContains([
            'item' => '/api/items/' . $item->getId(),
            'value' => $value,
            'type' => $type
        ]);
    }

    #[TestWith([DatumTypeEnum::TYPE_LINK, 'test'])]

    #[TestWith([DatumTypeEnum::TYPE_PRICE, '-8.50'])]
    #[TestWith([DatumTypeEnum::TYPE_PRICE, 'test'])]
    #[TestWith([DatumTypeEnum::TYPE_PRICE, '8,50'])]

    #[TestWith([DatumTypeEnum::TYPE_COUNTRY, 'ZZZ'])]
    #[TestWith([DatumTypeEnum::TYPE_DATE, '12-01-2023'])]

    #[TestWith([DatumTypeEnum::TYPE_RATING, '13'])]
    #[TestWith([DatumTypeEnum::TYPE_RATING, '1.5'])]
    #[TestWith([DatumTypeEnum::TYPE_RATING, '-1'])]

    #[TestWith([DatumTypeEnum::TYPE_NUMBER, '-8aaa.50'])]
    #[TestWith([DatumTypeEnum::TYPE_NUMBER, 'test'])]

    #[TestWith([DatumTypeEnum::TYPE_LIST, "['Value 1', 'Value 2']"])]
    #[TestWith([DatumTypeEnum::TYPE_CHOICE_LIST, 'test'])]
    #[TestWith([DatumTypeEnum::TYPE_CHECKBOX, '9'])]
    #[TestWith([DatumTypeEnum::TYPE_IMAGE, 'test'])]
    #[TestWith([DatumTypeEnum::TYPE_SIGN, 'test'])]
    #[TestWith([DatumTypeEnum::TYPE_FILE, 'test'])]
    #[TestWith([DatumTypeEnum::TYPE_VIDEO, 'test'])]
    public function test_datum_format_not_ok(string $type, mixed $value): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        $payload = [
            'item' => '/api/items/' . $item->getId(),
            'label' => 'Test label',
            'value' => $value,
            'type' => $type,
        ];

        if ($type === DatumTypeEnum::TYPE_CHOICE_LIST) {
            $choiceList = ChoiceListFactory::createOne(['name' => 'Progress', 'choices' => ['Value 1', 'Value 2', 'Value 3'], 'owner' => $user]);
            $payload['choiceList'] = '/api/choice_lists/' . $choiceList->getId();
        }

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/data', ['json' => $payload]);

        // Assert
        // Assert
        $this->assertResponseIsUnprocessable();
    }
}
