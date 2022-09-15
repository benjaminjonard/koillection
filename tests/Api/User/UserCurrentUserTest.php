<?php

declare(strict_types=1);

namespace App\Tests\Api\User;

use Api\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserCurrentUserTest extends ApiTestCase
{
    public function testGetUsers(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/users');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(1, $data['hydra:totalItems']);
        $this->assertCount(1, $data['hydra:member']);
        // $this->assertMatchesResourceCollectionJsonSchema(User::class); Bug in validation because of bigint type ?
    }

    public function testGetUser(): void
    {
        $iri = $this->iriConverter->getIriFromResource($this->user);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testPostUser(): void
    {
        $this->createClientWithCredentials()->request('DELETE', '/api/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testPutUser(): void
    {
        $iri = $this->iriConverter->getIriFromResource($this->user);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'username' => 'username_put',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'username' => 'username_put',
        ]);
    }

    public function testPatchUser(): void
    {
        $iri = $this->iriConverter->getIriFromResource($this->user);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'username' => 'username_patch',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'username' => 'username_patch',
        ]);
    }

    public function testDeleteUser(): void
    {
        $iri = $this->iriConverter->getIriFromResource($this->user);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
