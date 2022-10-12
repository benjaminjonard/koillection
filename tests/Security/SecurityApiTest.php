<?php

declare(strict_types=1);

namespace App\Tests\Security;

use Api\Tests\ApiTestCase;
use App\Entity\Collection;
use App\Factory\CollectionFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;

class SecurityApiTest extends ApiTestCase
{
    use Factories;

    public function test_successful_login(): void
    {
        // Arrange
        $user = UserFactory::createOne([
            'username' => 'user',
            'email' => 'user@test.com',
            'plainPassword' => 'koillection'
        ])->object();

        // Act
        $response = static::createClient()->request('POST', '/api/authentication_token', ['json' => [
            'username' => 'user',
            'password' => 'koillection'
        ]]);
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $data);
    }

    public function test_bad_credentials_login(): void
    {
        // Arrange
        UserFactory::createOne([
            'username' => 'user',
            'email' => 'user@test.com',
            'plainPassword' => 'koillection'
        ])->object();
        $client = static::createClient();

        // Act
        $response = $client->request('POST', '/api/authentication_token', ['json' => [
            'username' => 'user',
            'password' => 'password'
        ]]);

        // Assert
        $this->assertEquals($response->getStatusCode(), 401);
    }
}