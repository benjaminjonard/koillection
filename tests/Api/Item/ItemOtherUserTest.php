<?php

namespace App\Tests\Api\Item;

use Api\Tests\AuthenticatedTest;
use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Loan;
use App\Entity\Tag;
use Symfony\Component\HttpFoundation\Response;

class ItemOtherUserTest extends AuthenticatedTest
{
    public function testCantGetAnotherUserItem(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserItemCollection(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $this->createClientWithCredentials()->request('GET', $iri . '/collection');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserItemData(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/data');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Datum::class);
    }

    public function testCantGetAnotherUserItemLoans(): void
    {
        $item = $this->em->getRepository(Loan::class)->findBy(['owner' => $this->otherUser], [], 1)[0]->getItem();
        $iri = $this->iriConverter->getIriFromItem($item);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/loans');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Loan::class);
    }

    public function testCantGetAnotherUserItemRelatedItems(): void
    {
        $item = $this->em->getRepository(Item::class)->findOneWithRelatedItemsByUser($this->otherUser);
        $iri = $this->iriConverter->getIriFromItem($item);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/related_items');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function testCantGetAnotherUserItemTags(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/tags');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Tag::class);
    }


    public function testCantPutAnotherUserItem(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserItem(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'updated name with PATCH',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserItem(): void
    {
        $item = $this->em->getRepository(Item::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($item);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}