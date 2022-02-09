<?php

namespace App\Tests\Api\Item;

use Api\Tests\AuthenticatedTest;
use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Loan;
use App\Entity\Tag;
use Symfony\Component\HttpFoundation\Response;

class ItemCurrentUserTest extends AuthenticatedTest
{
    public function testGetItems(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/items');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(50, $data['hydra:totalItems']);
        $this->assertCount(30, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function testGetItem(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri
        ]);
    }

    public function testGetItemData(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/data');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Datum::class);
    }

    public function testGetItemLoans(): void
    {
        $item = $this->em->getRepository(Loan::class)->findBy(['owner' => $this->user], [], 1)[0]->getItem();
        $iri = $this->iriConverter->getIriFromItem($item);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/loans');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $data['hydra:totalItems']);
        $this->assertCount(1, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Loan::class);
    }

    public function testGetItemRelatedItems(): void
    {
        $item = $this->em->getRepository(Item::class)->findOneWithRelatedItemsByUser($this->user);
        $iri = $this->iriConverter->getIriFromItem($item);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/related_items');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $data['hydra:totalItems']);
        $this->assertCount(1, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function testGetItemTags(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/tags');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $data['hydra:totalItems']);
        $this->assertCount(1, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Tag::class);
    }

    public function testPutItem(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name with PUT',
        ]);
    }

    public function testPatchItem(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'updated name with PATCH',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name with PATCH',
        ]);
    }

    public function testDeleteItem(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}