<?php

declare(strict_types=1);

namespace App\Tests\Api\Wishlist;

use Api\Tests\ApiTestCase;
use App\Entity\Wish;
use App\Entity\Wishlist;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Response;

class WishlistCurrentUserTest extends ApiTestCase
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

    public function testGetWishlist(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
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

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/childrens');
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

        $this->createClientWithCredentials()->request('GET', $iri.'/parent');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Wishlist::class);
    }

    public function testGetWishlistWishes(): void
    {
        $wishlist = $this->em->getRepository(Wishlist::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($wishlist);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/wishes');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(5, $data['hydra:totalItems']);
        $this->assertCount(5, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Wish::class);
    }

    public function testPostWishlist(): void
    {
        $this->createClientWithCredentials()->request('POST', '/api/wishlists', [
            'headers' => ['Content-Type: multipart/form-data'],
            'json' => [
                'name' => 'New wishlist',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'name' => 'New wishlist',
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
            ],
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
}
