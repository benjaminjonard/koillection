<?php

namespace App\Tests\Api;

use Api\Tests\AuthenticatedTest;
use App\Entity\TagCategory;
use Symfony\Component\HttpFoundation\Response;

class TagCategoryTest extends AuthenticatedTest
{
    public function testGetTagCategorys(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/tag_categories');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(TagCategory::class);
    }

    // Interacting with current User's tagCategories
    public function testGetTagCategory(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri
        ]);
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
            ]
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

    // Interacting with another User's tagCategories
    public function testCantGetAnotherUserTagCategory(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPutAnotherUserTagCategory(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'label' => 'updated label with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserTagCategory(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'label' => 'updated label with PATCH',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserTagCategory(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}