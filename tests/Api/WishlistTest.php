<?php

namespace App\Tests\Api;

use Api\Tests\AuthenticatedTest;
use App\Entity\Wishlist;

class WishlistTest extends AuthenticatedTest
{
    public function testGetWishlists(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/wishlists');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wishlist::class);
    }

    // Interacting with current User's wishlists
    public function testGetWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri
        ]);
    }

    public function testPutWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name with PUT',
        ]);
    }

    public function testPatchWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'updated name with PATCH',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name with PATCH',
        ]);
    }

    public function testDeleteWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
    }

    // Interacting with another User's wishlists
    public function testCantGetAnotherUserWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCantPutAnotherUserWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCantPatchAnotherUserWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'updated name with PATCH',
            ]
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCantDeleteAnotherUserWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(404);
    }
}