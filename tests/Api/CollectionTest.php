<?php

namespace App\Tests\Api;

use Api\Tests\AuthenticatedTest;
use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Response;

class CollectionTest extends AuthenticatedTest
{
    public function testGetCollections(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/collections');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Collection::class);
    }

    // Interacting with current User's collections
    public function testGetCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri
        ]);
    }

    public function testGetCollectionChildren(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->user))
        ;
        $collection = $this->em->getRepository(Collection::class)->matching($criteria)[0]->getParent();
        $iri = $this->iriConverter->getIriFromItem($collection);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/childrens');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $data['hydra:totalItems']);
        $this->assertCount(1, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Collection::class);
    }

    public function testGetCollectionParent(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->user))
        ;
        $collection = $this->em->getRepository(Collection::class)->matching($criteria)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $this->createClientWithCredentials()->request('GET', $iri . '/parent');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Collection::class);
    }

    public function testGetCollectionItems(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/items');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(5, $data['hydra:totalItems']);
        $this->assertCount(5, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function testGetCollectionData(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/data');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Datum::class);
    }

    public function testPutCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'title' => 'updated title with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'title' => 'updated title with PUT',
        ]);
    }

    public function testPatchCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'updated title with PATCH',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'title' => 'updated title with PATCH',
        ]);
    }

    public function testDeleteCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    // Interacting with another User's collections
    public function testCantGetAnotherUserCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserCollectionChildren(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->otherUser))
        ;
        $collection = $this->em->getRepository(Collection::class)->matching($criteria)[0]->getParent();
        $iri = $this->iriConverter->getIriFromItem($collection);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/childrens');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Collection::class);
    }

    public function testCantGetAnotherUserCollectionParent(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->otherUser))
        ;
        $collection = $this->em->getRepository(Collection::class)->matching($criteria)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $this->createClientWithCredentials()->request('GET', $iri . '/parent');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserCollectionItems(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/items');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function testCantGetAnotherUserCollectionData(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/data');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Datum::class);
    }


    public function testCantPutAnotherUserCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'title' => 'updated title with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'updated title with PATCH',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}