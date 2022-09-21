<?php

declare(strict_types=1);

namespace App\Tests\Api\Field;

use Api\Tests\ApiTestCase;
use App\Entity\Field;
use Symfony\Component\HttpFoundation\Response;

class FieldOtherUserTest extends ApiTestCase
{
    public function testCantGetAnotherUserField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($field);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserFieldTemplate(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($field);

        $this->createClientWithCredentials()->request('GET', $iri.'/template');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPutAnotherUserField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($field);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($field);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'updated name with PATCH',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($field);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
