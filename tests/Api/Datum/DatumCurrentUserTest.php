<?php

declare(strict_types=1);

namespace App\Tests\Api\Datum;

use Api\Tests\ApiTestCase;
use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Enum\DatumTypeEnum;
use Symfony\Component\HttpFoundation\Response;

class DatumCurrentUserTest extends ApiTestCase
{
    public function testGetData(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/data');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(600, $data['hydra:totalItems']);
        $this->assertCount(30, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Datum::class);
    }

    public function testGetDatum(): void
    {
        $datum = $this->em->getRepository(Datum::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($datum);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testGetDatumItem(): void
    {
        $datum = $this->em->getRepository(Item::class)->findBy(['owner' => $this->user], [], 1)[0]->getData()[0];
        $iri = $this->iriConverter->getIriFromItem($datum);

        $this->createClientWithCredentials()->request('GET', $iri.'/item');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Item::class);
    }

    public function testGetDatumCollection(): void
    {
        $datum = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0]->getData()[0];
        $iri = $this->iriConverter->getIriFromItem($datum);

        $this->createClientWithCredentials()->request('GET', $iri.'/collection');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Collection::class);
    }

    public function testPostDatumWithCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $collectionIri = $this->iriConverter->getIriFromItem($collection);

        $this->createClientWithCredentials()->request('POST', '/api/data', [
            'headers' => ['Content-Type: multipart/form-data'],
            'json' => [
                'label' => 'New datum with collection',
                'type' => DatumTypeEnum::TYPE_TEXT,
                'collection' => $collectionIri
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'label' => 'New datum with collection'
        ]);
    }

    public function testPostDatumWithItem(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->user], [], 1)[0];
        $itemIri = $this->iriConverter->getIriFromItem($item);

        $this->createClientWithCredentials()->request('POST', '/api/data', [
            'headers' => ['Content-Type: multipart/form-data'],
            'json' => [
                'label' => 'New datum with item',
                'type' => DatumTypeEnum::TYPE_TEXT,
                'item' => $itemIri
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'label' => 'New datum with item'
        ]);
    }

    public function testPostDatumWithCollectionAndItem(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $collectionIri = $this->iriConverter->getIriFromItem($collection);

        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->user], [], 1)[0];
        $itemIri = $this->iriConverter->getIriFromItem($item);

        $this->createClientWithCredentials()->request('POST', '/api/data', [
            'headers' => ['Content-Type: multipart/form-data'],
            'json' => [
                'label' => 'New datum with item',
                'type' => DatumTypeEnum::TYPE_TEXT,
                'collection' => $collectionIri,
                'item' => $itemIri
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonContains([
            'hydra:description' => $this->translator->trans('error.datum.cant_be_used_by_both_collections_and_items', [], 'validators')
        ]);
    }

    public function testPostDatumWithoutCollectionNorItem(): void
    {
        $this->createClientWithCredentials()->request('POST', '/api/data', [
            'headers' => ['Content-Type: multipart/form-data'],
            'json' => [
                'label' => 'New datum with item',
                'type' => DatumTypeEnum::TYPE_TEXT
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonContains([
            'hydra:description' => $this->translator->trans('error.datum.must_provide_collection_or_item', [], 'validators')
        ]);
    }

    public function testPutDatum(): void
    {
        $datum = $this->em->getRepository(Datum::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($datum);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'label' => 'updated label with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'label' => 'updated label with PUT',
        ]);
    }

    public function testPatchDatum(): void
    {
        $datum = $this->em->getRepository(Datum::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($datum);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'label' => 'updated label with PATCH',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'label' => 'updated label with PATCH',
        ]);
    }

    public function testDeleteDatum(): void
    {
        $datum = $this->em->getRepository(Datum::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($datum);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
