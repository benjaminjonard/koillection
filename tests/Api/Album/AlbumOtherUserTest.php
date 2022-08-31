<?php

declare(strict_types=1);

namespace App\Tests\Api\Album;

use Api\Tests\ApiTestCase;
use App\Entity\Album;
use App\Entity\Photo;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Response;

class AlbumOtherUserTest extends ApiTestCase
{
    public function testCantGetAnotherUserAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserAlbumChildren(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->otherUser))
        ;
        $album = $this->em->getRepository(Album::class)->matching($criteria)[0]->getParent();
        $iri = $this->iriConverter->getIriFromItem($album);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/childrens');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Album::class);
    }

    public function testCantGetAnotherUserAlbumParent(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->otherUser))
        ;
        $album = $this->em->getRepository(Album::class)->matching($criteria)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('GET', $iri.'/parent');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserAlbumPhotos(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/photos');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Photo::class);
    }

    public function testCantPutAnotherUserAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'title' => 'updated title with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'updated title with PATCH',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserAlbum(): void
    {
        $album = $this->em->getRepository(Album::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($album);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
