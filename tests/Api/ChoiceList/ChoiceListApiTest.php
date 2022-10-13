<?php

declare(strict_types=1);

namespace App\Tests\Api\ChoiceList;

use Api\Tests\ApiTestCase;
use App\Entity\ChoiceList;
use App\Factory\ChoiceListFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class ChoiceListApiTest extends ApiTestCase
{
    use Factories;

    public function test_get_choice_lists(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        ChoiceListFactory::createMany(3, ['owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/choice_lists');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(ChoiceList::class);
    }

    public function test_get_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $choiceList = ChoiceListFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/choice_lists/' . $choiceList->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(ChoiceList::class);
        $this->assertJsonContains([
            'id' => $choiceList->getId()
        ]);
    }

    public function test_post_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/choice_lists', ['json' => [
            'name' => 'Progress',
            'choices' => ['New', 'In progress', 'Done']
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(ChoiceList::class);
        $this->assertJsonContains([
            'name' => 'Progress',
            'choices' => ['New', 'In progress', 'Done']
        ]);
    }

    public function test_put_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $choiceList = ChoiceListFactory::createOne(['name' => 'Progress', 'choices' => ['New', 'In progress', 'Done'], 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/choice_lists/'.$choiceList->getId(), ['json' => [
            'name' => 'Progress',
            'choices' => ['New', 'In progress', 'Done', 'Abandoned']
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(ChoiceList::class);
        $this->assertJsonContains([
            'id' => $choiceList->getId(),
            'name' => 'Progress',
            'choices' => ['New', 'In progress', 'Done', 'Abandoned']
        ]);
    }

    public function test_patch_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $choiceList = ChoiceListFactory::createOne(['name' => 'Progress', 'choices' => ['New', 'In progress', 'Done'], 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/choice_lists/'.$choiceList->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'choices' => ['New', 'In progress', 'Done', 'Abandoned']
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(ChoiceList::class);
        $this->assertJsonContains([
            'id' => $choiceList->getId(),
            'name' => 'Progress',
            'choices' => ['New', 'In progress', 'Done', 'Abandoned']
        ]);
    }

    public function test_delete_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $choiceList = ChoiceListFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/choice_lists/'.$choiceList->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}