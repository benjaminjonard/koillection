<?php

declare(strict_types=1);

namespace App\Tests\Api\Loan;

use App\Entity\Item;
use App\Entity\Loan;
use App\Tests\ApiTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\LoanFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoanApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_get_loans(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        LoanFactory::createMany(3, ['item' => $item, 'owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/loans');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Loan::class);
    }

    public function test_get_loan(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $loan = LoanFactory::createOne(['item' => $item, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/loans/' . $loan->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Loan::class);
        $this->assertJsonContains([
            'id' => $loan->getId()
        ]);
    }

    public function test_get_loan_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $loan = LoanFactory::createOne(['item' => $item, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/loans/' . $loan->getId() . '/item');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Item::class);
        $this->assertJsonContains([
            'id' => $item->getId()
        ]);
    }

    public function test_post_loan(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/loans', ['json' => [
            'lentTo' => 'Someone',
            'lentAt' => '2022-10-01 00:00:00',
            'item' => '/api/items/' . $item->getId(),
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Loan::class);
        $this->assertJsonContains([
            'lentTo' => 'Someone',
            'item' => '/api/items/' . $item->getId(),
        ]);
    }

    public function test_put_loan(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $loan = LoanFactory::createOne(['item' => $item, 'lentTo' => 'Someone', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/loans/' . $loan->getId(), ['json' => [
            'lentTo' => 'Someone else',
            'item' => '/api/items/' . $item->getId(),
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Loan::class);
        $this->assertJsonContains([
            'id' => $loan->getId(),
            'lentTo' => 'Someone else',
            'item' => '/api/items/' . $item->getId(),
        ]);
    }

    public function test_patch_loan(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $loan = LoanFactory::createOne(['item' => $item, 'lentTo' => 'Someone', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/loans/' . $loan->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'lentTo' => 'Someone else',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Loan::class);
        $this->assertJsonContains([
            'id' => $loan->getId(),
            'item' => '/api/items/' . $item->getId(),
            'lentTo' => 'Someone else',
        ]);
    }

    public function test_delete_loan(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $loan = LoanFactory::createOne(['item' => $item, 'lentTo' => 'Someone', 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/loans/' . $loan->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
