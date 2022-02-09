<?php

namespace App\Tests\Api\Datum;

use Api\Tests\AuthenticatedTest;
use App\Entity\Datum;
use Symfony\Component\HttpFoundation\Response;

class DatumCurrentUserTest extends AuthenticatedTest
{
    public function testGetData(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/data');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $data['hydra:totalItems']);
        $this->assertCount(30, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Datum::class);
    }

    public function testGetDatum(): void
    {
        $datum = $this->em->getRepository(Datum::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($datum);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri
        ]);
    }

    public function testPutDatum(): void
    {
        $datum = $this->em->getRepository(Datum::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($datum);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'label' => 'updated label with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'label' => 'updated label with PUT',
        ]);
    }

    public function testPatchDatum(): void
    {
        $datum = $this->em->getRepository(Datum::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($datum);

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

    public function testDeleteDatum(): void
    {
        $datum = $this->em->getRepository(Datum::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($datum);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}