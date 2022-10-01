<?php

declare(strict_types=1);

namespace App\Tests\Api\Wish;

use Api\Tests\ApiTestCase;
use App\Entity\Wish;
use Symfony\Component\HttpFoundation\Response;

class WishOtherUserTest extends ApiTestCase
{
    public function testCantGetAnotherUserWish(): void
    {
        $wish = $this->em->getRepository(Wish::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($wish);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUseWishWishlist(): void
    {
        $wish = $this->em->getRepository(Wish::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($wish);

        $this->createClientWithCredentials()->request('GET', $iri.'/wishlist');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPutAnotherUserWish(): void
    {
        $wish = $this->em->getRepository(Wish::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($wish);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserWish(): void
    {
        $wish = $this->em->getRepository(Wish::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($wish);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'updated name with PATCH',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserWish(): void
    {
        $wish = $this->em->getRepository(Wish::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($wish);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
