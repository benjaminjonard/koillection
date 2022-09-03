<?php

declare(strict_types=1);

namespace App\Tests\Api\Photo;

use Api\Tests\ApiTestCase;
use App\Entity\Album;
use App\Entity\Photo;
use Symfony\Component\HttpFoundation\Response;

class PhotoCurrentUserTest extends ApiTestCase
{
    public function testGetPhotos(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/photos');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(50, $data['hydra:totalItems']);
        $this->assertCount(30, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Photo::class);
    }

    public function testGetPhoto(): void
    {
        $photo = $this->em->getRepository(Photo::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($photo);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testGetPhotoAlbum(): void
    {
        $photo = $this->em->getRepository(Photo::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($photo);

        $this->createClientWithCredentials()->request('GET', $iri.'/album');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Album::class);
    }

    public function testPostPhoto(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->user], [], 1)[0];
        $albumIri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('POST', '/api/photos', ['json' => [
                'title' => 'New photo',
                'album' => $albumIri,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'New photo',
        ]);
    }

    public function testPutPhoto(): void
    {
        $photo = $this->em->getRepository(Photo::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($photo);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'title' => 'updated title with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'title' => 'updated title with PUT',
        ]);
    }

    public function testPatchPhoto(): void
    {
        $photo = $this->em->getRepository(Photo::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($photo);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'updated title with PATCH',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'title' => 'updated title with PATCH',
        ]);
    }

    public function testDeletePhoto(): void
    {
        $photo = $this->em->getRepository(Photo::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($photo);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
