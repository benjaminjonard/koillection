<?php

namespace App\Tests\Api;

use Api\Tests\AuthenticatedTest;
use App\Entity\Wish;
use App\Entity\Wishlist;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Response;

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

    public function testGetWishlistChildren(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->user))
        ;
        $wishlist = $this->em->getRepository(Wishlist::class)->matching($criteria)[0]->getParent();
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/childrens');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $data['hydra:totalItems']);
        $this->assertCount(1, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wishlist::class);
    }

    public function testGetWishlistParent(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->user))
        ;
        $wishlist = $this->em->getRepository(Wishlist::class)->matching($criteria)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('GET', $iri . '/parent');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wishlist::class);
    }

    public function testGetWishlistWishes(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/wishes');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(5, $data['hydra:totalItems']);
        $this->assertCount(5, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wish::class);
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

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    // Interacting with another User's wishlists
    public function testCantGetAnotherUserWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserWishlistChildren(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->otherUser))
        ;
        $wishlist = $this->em->getRepository(Wishlist::class)->matching($criteria)[0]->getParent();
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/childrens');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wishlist::class);
    }

    public function testCantGetAnotherUserWishlistParent(): void
    {
        $criteria = (new Criteria())
            ->where(Criteria::expr()->neq('parent', null))
            ->andWhere(Criteria::expr()->eq('owner', $this->otherUser))
        ;
        $wishlist = $this->em->getRepository(Wishlist::class)->matching($criteria)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('GET', $iri . '/parent');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserWishlistWishes(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $response = $this->createClientWithCredentials()->request('GET', $iri . '/wishes');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wish::class);
    }

    public function testCantPutAnotherUserWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
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

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}