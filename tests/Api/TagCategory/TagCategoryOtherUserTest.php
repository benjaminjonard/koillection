<?php

declare(strict_types=1);

namespace App\Tests\Api\TagCategory;

use Api\Tests\AuthenticatedTest;
use App\Entity\Tag;
use App\Entity\TagCategory;
use Symfony\Component\HttpFoundation\Response;

class TagCategoryOtherUserTest extends AuthenticatedTest
{
    public function testCantGetAnotherUserTagCategory(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserTagCategoryTags(): void
    {
        $tagCategory = $this->em->getRepository(TagCategory::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($tagCategory);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/tags');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Tag::class);
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
            ],
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
