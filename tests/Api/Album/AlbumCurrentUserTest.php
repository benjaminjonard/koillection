<?php

declare(strict_types=1);

namespace App\Tests\Api\Album;

use Api\Tests\ApiTestCase;
use App\Entity\Album;
use App\Entity\Photo;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Response;

class AlbumCurrentUserTest extends ApiTestCase
{
    public function testGetAlbums(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/albums');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Album::class);
    }

    public function testGetAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($album);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testGetAlbumChildren(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->user))
        ;
        $album = $this->em->getRepository(Album::class)->matching($criteria)[0]->getParent();
        $iri = $this->iriConverter->getIriFromResource($album);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/children');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(1, $data['hydra:totalItems']);
        $this->assertCount(1, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Album::class);
    }

    public function testGetAlbumParent(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->user))
        ;
        $album = $this->em->getRepository(Album::class)->matching($criteria)[0];
        $iri = $this->iriConverter->getIriFromResource($album);

        $this->createClientWithCredentials()->request('GET', $iri.'/parent');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Album::class);
    }

    public function testGetAlbumPhotos(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($album);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/photos');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(5, $data['hydra:totalItems']);
        $this->assertCount(5, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Photo::class);
    }

    public function testPostAlbum(): void
    {
        $this->createClientWithCredentials()->request('POST', '/api/albums', ['json' => [
                'title' => 'New album',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'New album',
        ]);
    }

    public function testPutAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($album);

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
        $iri = $this->iriConverter->getIriFromResource($album);

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

    public function testDeleteAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($album);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
