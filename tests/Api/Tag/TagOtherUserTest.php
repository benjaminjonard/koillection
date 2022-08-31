<?php

declare(strict_types=1);

namespace App\Tests\Api\Tag;

use Api\Tests\ApiTestCase;
use App\Entity\Item;
use App\Entity\Tag;
use Symfony\Component\HttpFoundation\Response;

class TagOtherUserTest extends ApiTestCase
{
    public function testCantGetAnotherUserTag(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserTagTagCategory(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);

        $this->createClientWithCredentials()->request('GET', $iri.'/category');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserTagItems(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/items');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function testCantPutAnotherUserTag(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'label' => 'updated label with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserTag(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'label' => 'updated label with PATCH',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserTag(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
