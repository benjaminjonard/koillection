<?php

namespace App\Tests\Api;

use Api\Tests\AuthenticatedTest;
use App\Entity\Album;

class AlbumTest extends AuthenticatedTest
{
    public function testGetAlbums(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/albums');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Album::class);
    }

    // Interacting with current User's albums
    public function testGetAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri
        ]);
    }

    public function testPutAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'title' => 'updated title with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'title' => 'updated title with PUT',
        ]);
    }

    public function testPatchAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'updated title with PATCH',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'title' => 'updated title with PATCH',
        ]);
    }

    public function testDeleteAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
    }

    // Interacting with another User's albums
    public function testCantGetAnotherUserAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCantPutAnotherUserAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'title' => 'updated title with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCantPatchAnotherUserAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'updated title with PATCH',
            ]
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCantDeleteAnotherUserAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(404);
    }
}