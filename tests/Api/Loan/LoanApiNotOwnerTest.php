<?php

declare(strict_types=1);

namespace App\Tests\Api\Loan;

use Api\Tests\ApiTestCase;
use App\Factory\CollectionFactory;
use App\Factory\ItemFactory;
use App\Factory\LoanFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class LoanApiNotOwnerTest extends ApiTestCase
{
    use Factories;

    public function test_cant_get_another_user_loan(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $owner]);
        $loan = LoanFactory::createOne(['item' => $item, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/loans/'.$loan->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_post_loan_with_another_user_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/loans/', ['json' => [
            'lentTo' => 'Someone',
            'lentAt' => '2022-10-01T12:00:00+02:00',
            'item' => '/api/items/'.$item->getId()
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_put_another_user_loan(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $owner]);
        $loan = LoanFactory::createOne(['item' => $item, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/loans/'.$loan->getId(), ['json' => [
            'lentTo' => 'Someone else',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_loan(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $owner]);
        $loan = LoanFactory::createOne(['item' => $item, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/loans/'.$loan->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'lentTo' => 'Someone else',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_loan(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $owner]);
        $loan = LoanFactory::createOne(['item' => $item, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/loans/'.$loan->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
