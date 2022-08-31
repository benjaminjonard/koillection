<?php

declare(strict_types=1);

namespace App\Tests\Api\Log;

use Api\Tests\ApiTestCase;
use App\Entity\Log;
use Symfony\Component\HttpFoundation\Response;

class LogCurrentUserTest extends ApiTestCase
{
    public function testGetLogs(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/logs');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(215, $data['hydra:totalItems']);
        $this->assertCount(30, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Log::class);
    }

    public function testGetLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testPutLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => []]);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testPostLog(): void
    {
        $this->createClientWithCredentials()->request('POST', '/api/logs');

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testPatchLog(): void
    {
        $log = $this->em->getRepository(Log::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($log);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
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
