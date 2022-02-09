<?php

namespace App\Tests\Api;

use Api\Tests\AuthenticatedTest;
use App\Entity\Field;
use Symfony\Component\HttpFoundation\Response;

class FieldTest extends AuthenticatedTest
{
    public function testGetFields(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/fields');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(50, $data['hydra:totalItems']);
        $this->assertCount(30, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Field::class);
    }

    // Interacting with current User's fields
    public function testGetField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri
        ]);
    }

    public function testPutField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name with PUT',
        ]);
    }

    public function testPatchField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);

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

    public function testDeleteField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    // Interacting with another User's fields
    public function testCantGetAnotherUserField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPutAnotherUserField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'updated name with PATCH',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}