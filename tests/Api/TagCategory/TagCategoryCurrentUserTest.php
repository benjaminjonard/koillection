<?php

declare(strict_types=1);

namespace App\Tests\Api\TagCategory;

use Api\Tests\AuthenticatedTest;
use App\Entity\Tag;
use App\Entity\TagCategory;
use Symfony\Component\HttpFoundation\Response;

class TagCategoryCurrentUserTest extends AuthenticatedTest
{
    public function testGetTagCategories(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/tag_categories');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(TagCategory::class);
    }

    public function testGetTagCategory(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testGetTagCategoryTags(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/tags');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $data['hydra:totalItems']);
        $this->assertCount(1, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Tag::class);
    }

    public function testPutTagCategory(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'label' => 'updated label with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'label' => 'updated label with PUT',
        ]);
    }

    public function testPatchTagCategory(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);

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

    public function testDeleteTagCategory(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
