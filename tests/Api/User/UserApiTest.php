<?php

declare(strict_types=1);

namespace App\Tests\Api\User;

use App\Entity\User;
use App\Tests\ApiTestCase;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_user_can_see_itself(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/users/'.$user->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(User::class);
        $this->assertJsonContains([
            'id' => $user->getId()
        ]);
    }

    public function test_user_cant_see_other_user(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $otherUser = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/users/'.$otherUser->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_post_user(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/users');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_put_log(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/users/'.$user->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_patch_log(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/users/'.$user->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_delete_log(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/users/'.$user->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
