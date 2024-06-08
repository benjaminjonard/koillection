<?php

declare(strict_types=1);

namespace App\Tests\Api\Inventory;

use App\Entity\Inventory;
use App\Tests\ApiTestCase;
use App\Tests\Factory\InventoryFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class InventoryApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_get_inventories(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        InventoryFactory::createMany(3, ['owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/inventories');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Inventory::class);
    }

    public function test_get_inventory(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $inventory = InventoryFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/inventories/' . $inventory->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Inventory::class);
        $this->assertJsonContains([
            'id' => $inventory->getId()
        ]);
    }

    public function test_delete_inventory(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $inventory = InventoryFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/inventories/' . $inventory->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
