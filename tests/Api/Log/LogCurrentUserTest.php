<?php

namespace App\Tests\Api\Log;

use Api\Tests\AuthenticatedTest;
use App\Entity\Log;
use Symfony\Component\HttpFoundation\Response;

class LogCurrentUserTest extends AuthenticatedTest
{
    public function testGetLogs(): void
    {
        $this->createClientWithCredentials()->request('GET', '/api/logs');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Log::class);
    }

    public function testGetLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri
        ]);
    }

    public function testPutLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => []]);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testPatchLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json']
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testDeleteLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}