<?php

declare(strict_types=1);

namespace App\Tests\Api\ChoiceList;

use Api\Tests\ApiTestCase;
use App\Factory\ChoiceListFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class ChoiceListApiNotOwnerTest extends ApiTestCase
{
    use Factories;

    public function test_cant_get_another_user_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $choiceList = ChoiceListFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/choice_lists/'.$choiceList->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_put_another_user_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $choiceList = ChoiceListFactory::createOne(['name' => 'Progress', 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/choice_lists/'.$choiceList->getId(), ['json' => [
            'name' => 'Status',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $choiceList = ChoiceListFactory::createOne(['name' => 'Progress', 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/choice_lists/'.$choiceList->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'Status',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $choiceList = ChoiceListFactory::createOne(['name' => 'Progress', 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/choice_lists/'.$choiceList->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
