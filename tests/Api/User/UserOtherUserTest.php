<?php

namespace App\Tests\Api\User;

use Api\Tests\AuthenticatedTest;
use Symfony\Component\HttpFoundation\Response;

class UserOtherUserTest extends AuthenticatedTest
{
    public function testCantGetAnotherUser(): void
    {
        $iri = $this->iriConverter->getIriFromItem($this->otherUser);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPutAnotherUser(): void
    {
        $iri = $this->iriConverter->getIriFromItem($this->otherUser);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'username' => 'username_put',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUser(): void
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

    public function testCantDeleteAnotherUser(): void
    {
        $iri = $this->iriConverter->getIriFromItem($this->otherUser);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}