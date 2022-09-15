<?php

declare(strict_types=1);

namespace App\Tests\Api\Collection;

use Api\Tests\ApiTestCase;
use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Response;

class CollectionCurrentUserTest extends ApiTestCase
{
    public function testGetCollections(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/collections');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Collection::class);
    }

    public function testGetCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($collection);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testGetCollectionChildren(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->user))
        ;
        $collection = $this->em->getRepository(Collection::class)->matching($criteria)[0]->getParent();
        $iri = $this->iriConverter->getIriFromResource($collection);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/childrens');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(1, $data['hydra:totalItems']);
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
        $iri = $this->iriConverter->getIriFromResource($collection);

        $this->createClientWithCredentials()->request('GET', $iri.'/parent');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Collection::class);
    }

    public function testGetCollectionItems(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($collection);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/items');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(5, $data['hydra:totalItems']);
        $this->assertCount(5, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function testGetCollectionData(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($collection);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/data');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Datum::class);
    }

    public function testPostCollection(): void
    {
        $this->createClientWithCredentials()->request('POST', '/api/collections', ['json' => [
                'title' => 'New collection',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'New collection',
        ]);
    }

    public function testPutCollection(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($collection);

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
        $iri = $this->iriConverter->getIriFromResource($collection);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'updated title with PATCH',
            ],
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
        $iri = $this->iriConverter->getIriFromResource($collection);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
