<?php

declare(strict_types=1);

namespace App\Tests\Api\Tag;

use Api\Tests\ApiTestCase;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\TagCategory;
use Symfony\Component\HttpFoundation\Response;

class TagCurrentUserTest extends ApiTestCase
{
    public function testGetTags(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/tags');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Tag::class);
    }

    public function testGetTag(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testGetTagTagCategory(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);

        $this->createClientWithCredentials()->request('GET', $iri.'/category');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(TagCategory::class);
    }

    public function testGetTagItems(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/items');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(5, $data['hydra:totalItems']);
        $this->assertCount(5, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function testPostTag(): void
    {
        $this->createClientWithCredentials()->request('POST', '/api/tags', ['json' => [
                'label' => 'New tag',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'label' => 'New tag',
        ]);
    }

    public function testPutTag(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'label' => 'updated label with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'label' => 'updated label with PUT',
        ]);
    }

    public function testPatchTag(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);

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

    public function testDeleteTag(): void
    {
        $tag = $this->em->getRepository(Tag::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tag);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
