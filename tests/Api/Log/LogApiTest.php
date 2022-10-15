<?php

declare(strict_types=1);

namespace App\Tests\Api\Log;

use App\Entity\Log;
use App\Factory\LogFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LogApiTest extends ApiTestCase
{
    use Factories, ResetDatabase;

    public function test_get_logs(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        LogFactory::createMany(3, ['owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/logs');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Log::class);
    }

    public function test_get_log(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $log = LogFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/logs/'.$log->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Log::class);
        $this->assertJsonContains([
            'id' => $log->getId()
        ]);
    }

    public function test_post_log(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/logs');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_put_log(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $log = LogFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/logs/'.$log->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_patch_log(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $log = LogFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/logs/'.$log->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_delete_log(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $log = LogFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/logs/'.$log->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
