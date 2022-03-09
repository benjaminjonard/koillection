<?php

declare(strict_types=1);

namespace App\Tests\Api\Loan;

use Api\Tests\AuthenticatedTest;
use App\Entity\Item;
use App\Entity\Loan;
use Symfony\Component\HttpFoundation\Response;

class LoanCurrentUserTest extends AuthenticatedTest
{
    public function testGetLoans(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/loans');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(5, $data['hydra:totalItems']);
        $this->assertCount(5, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Loan::class);
    }

    public function testGetLoan(): void
    {
        $loan = $this->em->getRepository(Loan::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($loan);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testGetLoanItem(): void
    {
        $loan = $this->em->getRepository(Loan::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($loan);

        $this->createClientWithCredentials()->request('GET', $iri.'/item');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Item::class);
    }

    public function testPutLoan(): void
    {
        $loan = $this->em->getRepository(Loan::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($loan);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'lentTo' => 'updated lentTo with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'lentTo' => 'updated lentTo with PUT',
        ]);
    }

    public function testPatchLoan(): void
    {
        $loan = $this->em->getRepository(Loan::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($loan);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'lentTo' => 'updated lentTo with PATCH',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'lentTo' => 'updated lentTo with PATCH',
        ]);
    }

    public function testDeleteLoan(): void
    {
        $loan = $this->em->getRepository(Loan::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($loan);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
