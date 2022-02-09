<?php

namespace App\Tests\Api;

use Api\Tests\AuthenticatedTest;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends AuthenticatedTest
{
    public function testGetUsers(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/users');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $data['hydra:totalItems']);
        $this->assertCount(1, $data['hydra:member']);
        //$this->assertMatchesResourceCollectionJsonSchema(User::class); Bug in validation because of bigint type ?
    }

    // Interacting with current User's users
    public function testGetUser(): void
    {
        $iri = $this->iriConverter->getIriFromItem($this->user);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri
        ]);
    }

    public function testPutUser(): void
    {
        $iri = $this->iriConverter->getIriFromItem($this->user);

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
        $iri = $this->iriConverter->getIriFromItem($this->user);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'username' => 'username_patch',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'username' => 'username_patch',
        ]);
    }

    public function testDeleteUser(): void
    {
        $iri = $this->iriConverter->getIriFromItem($this->user);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    // Interacting with another User's users
    public function testCantGetAnotherUserUser(): void
    {
        $iri = $this->iriConverter->getIriFromItem($this->otherUser);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPutAnotherUserUser(): void
    {
        $iri = $this->iriConverter->getIriFromItem($this->otherUser);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'username' => 'username_put',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserUser(): void
    {
        $iri = $this->iriConverter->getIriFromItem($this->otherUser);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'username' => 'username_patch',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserUser(): void
    {
        $iri = $this->iriConverter->getIriFromItem($this->otherUser);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}