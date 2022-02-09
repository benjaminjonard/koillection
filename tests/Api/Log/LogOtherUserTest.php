<?php

namespace App\Tests\Api\Log;

use Api\Tests\AuthenticatedTest;
use App\Entity\Log;
use Symfony\Component\HttpFoundation\Response;

class LogOtherUserTest extends AuthenticatedTest
{
    public function testCantGetAnotherUserLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPutAnotherUserLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'lentTo' => 'updated lentTo with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testCantPatchAnotherUserLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json']
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testCantDeleteAnotherUserLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}