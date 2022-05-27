<?php

declare(strict_types=1);

namespace App\Tests\Api\Collection;

use Api\Tests\ApiTestCase;
use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Response;

class CollectionOtherUserTest extends ApiTestCase
{
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

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/childrens');
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

        $this->createClientWithCredentials()->request('GET', $iri.'/parent');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserCollectionItems(): void
    {
        $collection = $this->em->getRepository(Collection::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($collection);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/items');
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

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/data');
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
            ],
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
