<?php

namespace App\Tests\Api\Inventory;

use Api\Tests\AuthenticatedTest;
use App\Entity\Inventory;
use Symfony\Component\HttpFoundation\Response;

class InventoryOtherUserTest extends AuthenticatedTest
{
    public function testCantGetAnotherUserInventory(): void
    {
        $inventory = $this->em->getRepository(Inventory::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($inventory);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPutAnotherUserInventory(): void
    {
        $inventory = $this->em->getRepository(Inventory::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($inventory);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserInventory(): void
    {
        $inventory = $this->em->getRepository(Inventory::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($inventory);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'updated name with PATCH',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserInventory(): void
    {
        $inventory = $this->em->getRepository(Inventory::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($inventory);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}